<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\PageApprovalRequest;
use App\Models\PageApprovalRequestLog;
use App\Models\Pages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PageApprovalController extends Controller
{
    private function isSuperAdmin($user)
    {
        if (!$user || !$user->role) {
            return false;
        }

        $roleTitle = strtolower(trim($user->role->role));
        return $roleTitle === 'super admin' || $roleTitle === 'superadmin' || Str::contains($roleTitle, 'super');
    }

    private function canReview($requestModel, $user)
    {
        return $requestModel->current_approver_id == $user->id || $this->isSuperAdmin($user);
    }

    private function getFieldLabels()
    {
        return [
            'page_url' => 'Page Url',
            'page_name' => 'Reference Name',
            'slug' => 'Slug',
            'seo_title' => 'Title',
            'seo_description' => 'Meta Description',
            'seo_keywords' => 'Meta Keywords',
            'og_meta_title' => 'OG Meta Title',
            'og_meta_description' => 'OG Meta Description',
            'og_meta_image_url' => 'OG Meta Image Url',
            'twitter_card_title' => 'Twitter Card Title',
            'twitter_card_description' => 'Twitter Card Description',
            'schema_markup' => 'Schema Markup',
            'header_content' => 'Header Content',
            'center_content' => 'Center Content',
            'footer_content' => 'Footer Content',
            'page_script' => 'Page Script',
        ];
    }

    public function index(Request $request)
    {
        $user = auth()->user()->load('role');
        $isSuperAdmin = $this->isSuperAdmin($user);

        $page_title = 'Approval Requests';
        $page_description = 'Review pending SEO/content changes';
        $breadcrumbs = [
            [
                'title' => 'Approval Requests',
                'url' => '',
            ],
        ];

        $query = PageApprovalRequest::with(['page', 'requester', 'currentApprover', 'managerApprover', 'adminApprover'])
            ->orderBy('id', 'desc');

        if (!$isSuperAdmin) {
            $query->where(function ($q) use ($user) {
                $q->whereIn('status', ['pending_manager', 'pending_admin'])
                    ->where('current_approver_id', $user->id)
                    ->orWhere('requested_by', $user->id);
            });
        }

        $requests = $query->get();

        return view('admin.pages.page_approval.list', compact('page_title', 'page_description', 'breadcrumbs', 'requests', 'isSuperAdmin'));
    }

    public function review($id)
    {
        $user = auth()->user()->load('role');
        $isSuperAdmin = $this->isSuperAdmin($user);

        $requestDetails = PageApprovalRequest::with([
            'page',
            'requester',
            'currentApprover',
            'managerApprover',
            'adminApprover',
            'approver',
            'rejector',
            'overrideBy',
            'logs.actionBy',
        ])->where('id', $id)->firstOrFail();

        if (!$isSuperAdmin && $requestDetails->current_approver_id != $user->id && $requestDetails->requested_by != $user->id) {
            return redirect()->back()->withErrors(['You are not authorized to review this request.']);
        }

        $labels = $this->getFieldLabels();
        $oldPayload = $requestDetails->old_payload ?: [];
        $newPayload = $requestDetails->new_payload ?: [];
        $diffRows = [];

        foreach ($labels as $field => $label) {
            $oldValue = isset($oldPayload[$field]) ? (string) $oldPayload[$field] : '';
            $newValue = isset($newPayload[$field]) ? (string) $newPayload[$field] : '';
            $isChanged = trim($oldValue) !== trim($newValue);
            $diffRows[] = [
                'field' => $label,
                'old' => $oldValue,
                'new' => $newValue,
                'changed' => $isChanged,
            ];
        }

        $page_title = 'Review Approval Request';
        $page_description = 'Compare old vs proposed values';
        $breadcrumbs = [
            [
                'title' => 'Approval Requests',
                'url' => url('admin/page/approval-requests'),
            ],
            [
                'title' => 'Review',
                'url' => '',
            ],
        ];

        return view('admin.pages.page_approval.review', compact('page_title', 'page_description', 'breadcrumbs', 'requestDetails', 'diffRows', 'isSuperAdmin'));
    }

    public function approve(Request $request, $id)
    {
        $user = auth()->user()->load('role');
        $isSuperAdmin = $this->isSuperAdmin($user);

        $approvalRequest = PageApprovalRequest::where('id', $id)->firstOrFail();
        if (!$this->canReview($approvalRequest, $user)) {
            return redirect()->back()->withErrors(['You are not authorized to approve this request.']);
        }

        if (!in_array($approvalRequest->status, ['pending_manager', 'pending_admin'])) {
            return redirect()->back()->withErrors(['Only pending requests can be approved.']);
        }

        DB::beginTransaction();
        try {
            $fromStatus = $approvalRequest->status;
            $comments = $request->get('comments');

            if ($approvalRequest->status === 'pending_manager' && $approvalRequest->admin_approver_id && !$isSuperAdmin) {
                $approvalRequest->update([
                    'status' => 'pending_admin',
                    'current_approver_id' => $approvalRequest->admin_approver_id,
                    'approver_comments' => $comments,
                ]);

                PageApprovalRequestLog::create([
                    'request_id' => $approvalRequest->id,
                    'action_by' => $user->id,
                    'action' => 'approved_manager',
                    'from_status' => $fromStatus,
                    'to_status' => 'pending_admin',
                    'comments' => $comments,
                ]);
            } else {
                $page = Pages::where('id', $approvalRequest->page_id)->first();
                if (!$page) {
                    DB::rollback();
                    return redirect()->back()->withErrors(['Linked page was not found.']);
                }

                $payload = $approvalRequest->new_payload ?: [];
                $payload['updated_by'] = $user->id;
                $page->update($payload);

                $approvalRequest->update([
                    'status' => 'approved',
                    'current_approver_id' => null,
                    'approved_by' => $user->id,
                    'overridden_by' => $isSuperAdmin ? $user->id : null,
                    'approver_comments' => $comments,
                    'reviewed_at' => now(),
                    'published_at' => now(),
                ]);

                PageApprovalRequestLog::create([
                    'request_id' => $approvalRequest->id,
                    'action_by' => $user->id,
                    'action' => $isSuperAdmin ? 'overridden_approved' : 'approved_admin',
                    'from_status' => $fromStatus,
                    'to_status' => 'approved',
                    'comments' => $comments,
                ]);
            }

            DB::commit();
            return redirect('admin/page/approval-requests')->with('success', 'Approval action completed successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors([$e->getMessage()]);
        }
    }

    public function reject(Request $request, $id)
    {
        $user = auth()->user()->load('role');
        $isSuperAdmin = $this->isSuperAdmin($user);

        $approvalRequest = PageApprovalRequest::where('id', $id)->firstOrFail();
        if (!$this->canReview($approvalRequest, $user)) {
            return redirect()->back()->withErrors(['You are not authorized to reject this request.']);
        }

        if (!in_array($approvalRequest->status, ['pending_manager', 'pending_admin'])) {
            return redirect()->back()->withErrors(['Only pending requests can be rejected.']);
        }

        $comments = $request->get('comments');

        DB::beginTransaction();
        try {
            $fromStatus = $approvalRequest->status;
            $approvalRequest->update([
                'status' => 'rejected',
                'current_approver_id' => null,
                'rejected_by' => $user->id,
                'overridden_by' => $isSuperAdmin ? $user->id : null,
                'approver_comments' => $comments,
                'reviewed_at' => now(),
            ]);

            PageApprovalRequestLog::create([
                'request_id' => $approvalRequest->id,
                'action_by' => $user->id,
                'action' => $isSuperAdmin ? 'overridden_rejected' : 'rejected',
                'from_status' => $fromStatus,
                'to_status' => 'rejected',
                'comments' => $comments,
            ]);

            DB::commit();
            return redirect('admin/page/approval-requests')->with('success', 'Request rejected successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors([$e->getMessage()]);
        }
    }
}
