@extends('admin.layout.default')

@section('modulemaster', 'active menu-item-open')
@section('content')
    <div class="card card-custom">

        <div class="card-body">
            <div class="mb-7">
                <div class="row align-items-center">

                    <form method="POST" action="" class="w-100">
                        {{ csrf_field() }}

                        <div class="px-4">
                            <div class="form-group col-md-12">
                                <label>Module Name</label>
                                <div><input type="text" name="name" placeholder="Enter Module Name"
                                        class="form-control module_name" isrequired="isrequired"
                                        value="{{ $moduleDetail->name }}">

                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <h3 class="mt-3 text-center">BUILD MODULE</h3>
                                </div>
                                <div class="form-group col-md-12">
                                    <select name="formTemplates" id="formTemplates" class="form-control">

                                        <option value="Custom">
                                            {{ $moduleDetail->module_code != '' ? 'Custom' : '--select form--' }}</option>

                                        <option value="default">Default Form</option>
                                        <option value="medicalForm">Medical Form</option>
                                        <option value="category">Category Form</option>
                                    </select>
                                </div>
                            </div>

                        </div>
                        <div class="px-4">
                            <div class="alert alert-success d-none">
                                <i class="fas fa-check-double error-icons"></i><strong class="responsealert"></strong>
                            </div>
                            <div class="alert alert-danger d-none">
                                <i class="fas fa-check-double error-icons"></i><strong class="responsealert"></strong>
                            </div>
                            <div id="build-wrap"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </div>
@endsection

{{-- Styles Section --}}
@section('styles')
    <style>
        #formTemplates {
            margin: 10px auto;
            max-width: 500px;
        }

        code {
            background-color: #000000 !important;
        }

        /* [data-type="autocomplete"],
                                                                                            [data-type="button"],
                                                                                            [data-type="checkbox-group"],
                                                                                            [data-type="radio-group"],
                                                                                            [data-type="select"],
                                                                                            [data-type="file"] {
                                                                                                display: none;
                                                                                            } */
    </style>
@endsection



{{-- Scripts Section --}}
@section('scripts')
    <script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
    <script src="{{ asset('form-builder/form-builder.min.js') }}"></script>

    <script>
        jQuery(function($) {

            const $fbTemplate = $(document.getElementById("build-wrap"));
            const templateSelect = document.getElementById("formTemplates");
            const code = {!! $moduleDetail->module_code ?? '[]' !!};
            const isCustomTemplate = code !== '';

            const templates = {
                Custom: code,
                default: [{
                        "type": "checkbox-group",
                        "required": false,
                        "label": "Show on top<br>",
                        "toggle": false,
                        "inline": false,
                        "name": "show_on_top",
                        "access": false,
                        "other": false,
                        "values": [{
                            "label": "Show on top",
                            "value": "",
                            "selected": false
                        }]
                    },
                    {
                        "type": "checkbox-group",
                        "required": false,
                        "label": "Is Trending",
                        "toggle": false,
                        "inline": false,
                        "name": "Is_Trending",
                        "access": true,
                        "other": false,
                        "values": [{
                            "label": " Is Trending",
                            "value": "",
                            "selected": false
                        }]
                    },
                    {
                        "type": "text",
                        "required": true,
                        "label": "Test Name",
                        "placeholder": "Enter Test Name",
                        "className": "form-control",
                        "name": "test_name",
                        "access": false,
                        "subtype": "text"
                    },
                    {
                        "type": "text",
                        "required": false,
                        "label": "Test Alias Name",
                        "placeholder": "eg. Philadelphia Quant PCR, Kinase Domain test",
                        "className": "form-control",
                        "name": "test_alias_name",
                        "access": false,
                        "subtype": "text"
                    },
                    {
                        "type": "text",
                        "required": true,
                        "label": "Test Code",
                        "placeholder": "Enter Test Code",
                        "className": "form-control",
                        "name": "test_code",
                        "access": false,
                        "subtype": "text"
                    },
                    {
                        "type": "text",
                        "required": false,
                        "label": "Test Type",
                        "placeholder": "Enter Test Type",
                        "className": "form-control",
                        "name": "test_type",
                        "access": false,
                        "subtype": "text"
                    },
                    {
                        "type": "text",
                        "required": true,
                        "label": "Lab Name",
                        "placeholder": "Enter Lab Name",
                        "className": "form-control",
                        "name": "lab_name",
                        "access": false,
                        "subtype": "text"
                    }
                ],
                category: [{
                        "type": "text",
                        "required": true,
                        "label": "Category Name<br>",
                        "placeholder": "Enter Category Name",
                        "className": "form-control",
                        "name": "category_name",
                        "access": false,
                        "subtype": "text"
                    },
                    {
                        "type": "text",
                        "required": false,
                        "label": "Short Description<br>",
                        "placeholder": "Enter Short Description",
                        "className": "form-control",
                        "name": "short_description",
                        "access": false,
                        "subtype": "text"
                    },
                    {
                        "type": "file",
                        "required": false,
                        "label": "<div>Category Icon (Width: 96px, Height:96px)</div>",
                        "className": "form-control",
                        "name": "category_Icon",
                        "access": false,
                        "multiple": false
                    }
                ],
                medicalForm: [{
                        "type": "text",
                        "required": true,
                        "label": "Full Name",
                        "placeholder": "Enter Your Full Name*",
                        "className": "form-control",
                        "name": "full_name",
                        "access": false,
                        "subtype": "text"
                    },
                    {
                        "type": "text",
                        "required": true,
                        "label": "<span style=\"color: rgb(44, 51, 69); font-family: Inter, sans-serif; font-size: 16px;\">What is your age?</span>",
                        "placeholder": "ex: 23",
                        "className": "form-control",
                        "name": "age",
                        "access": false,
                        "subtype": "text"
                    },
                    {
                        "type": "select",
                        "required": true,
                        "label": "<span style=\"color: rgb(44, 51, 69); font-family: Inter, sans-serif; font-size: 16px;\">What is your gender?</span>",
                        "className": "form-control",
                        "name": "gender",
                        "access": false,
                        "multiple": false,
                        "values": [{
                                "label": "--select your gender--",
                                "value": "",
                                "selected": true
                            },
                            {
                                "label": "Male",
                                "value": "male",
                                "selected": false
                            },
                            {
                                "label": "Female",
                                "value": "female",
                                "selected": false
                            },
                            {
                                "label": "N/A",
                                "value": "n/a",
                                "selected": false
                            }
                        ]
                    },
                    {
                        "type": "number",
                        "required": true,
                        "label": "<label class=\"form-label form-label-top form-label-auto\" id=\"label_4\" for=\"input_4_full\" style=\"font-weight: 500; word-break: break-word; width: 308px; margin-left: 2px; margin-bottom: 14px; color: rgb(44, 51, 69); font-family: Inter, sans-serif; text-align: left; font-size: 16px; text-transform: none; text-wrap: wrap;\">Contact Number</label><div id=\"cid_4\" class=\"form-input-wide\" data-layout=\"half\" style=\"width: 310px; color: rgb(44, 51, 69); font-family: Inter, sans-serif; font-size: 16px;\"><span class=\"form-sub-label-container\" style=\"flex: 1 1 100%; vertical-align: top;\"></span></div>",
                        "placeholder": "eg:8989565256",
                        "className": "form-control",
                        "name": "number",
                        "access": false,
                        "subtype": "number"
                    },
                    {
                        "type": "text",
                        "subtype": "email",
                        "required": true,
                        "label": "<label class=\"form-label form-label-top form-label-auto\" id=\"label_13\" for=\"input_13\" aria-hidden=\"false\" style=\"font-weight: 500; word-break: break-word; width: 308px; margin-left: 2px; margin-bottom: 14px; color: rgb(44, 51, 69); font-family: Inter, sans-serif; text-align: left; font-size: 16px; text-transform: none; text-wrap: wrap;\">Email Address</label><div id=\"cid_13\" class=\"form-input-wide\" data-layout=\"half\" style=\"width: 310px; color: rgb(44, 51, 69); font-family: Inter, sans-serif; font-size: 16px;\"><span class=\"form-sub-label-container\" style=\"flex: 1 1 100%; vertical-align: top;\"></span></div>",
                        "placeholder": "Enter your e-mail id",
                        "className": "form-control",
                        "name": "email",
                        "access": false
                    },
                    {
                        "type": "checkbox-group",
                        "required": false,
                        "label": "<div>Check the conditions that apply to you or any member of your immediate relatives:</div>",
                        "toggle": false,
                        "inline": false,
                        "name": "conditions",
                        "access": false,
                        "other": false,
                        "values": [{
                                "label": "Asthma",
                                "value": "Asthma",
                                "selected": false
                            },
                            {
                                "label": "Cancer",
                                "value": "Cancer",
                                "selected": false
                            },
                            {
                                "label": "Cardiac disease",
                                "value": "Cardiac disease",
                                "selected": false
                            },
                            {
                                "label": "Diabetes",
                                "value": "Diabetes",
                                "selected": false
                            },
                            {
                                "label": "Hypertension",
                                "value": "Hypertension",
                                "selected": false
                            },
                            {
                                "label": "Psychiatric disorder",
                                "value": "Psychiatric disorder",
                                "selected": false
                            },
                            {
                                "label": "Epilepsy",
                                "value": "Epilepsy",
                                "selected": false
                            },
                            {
                                "label": "Psychiatric disorder",
                                "value": "Psychiatric disorder",
                                "selected": false
                            },
                            {
                                "label": "Other",
                                "value": "Other",
                                "selected": false
                            }
                        ]
                    },
                    {
                        "type": "checkbox-group",
                        "required": true,
                        "label": "Check the symptoms that you' re currently experiencing:<br>",
                        "toggle": false,
                        "inline": false,
                        "name": "symptoms",
                        "access": false,
                        "other": false,
                        "values": [{
                                "label": "Chest pain",
                                "value": "Chest pain",
                                "selected": false
                            },
                            {
                                "label": "Respiratory",
                                "value": "Respiratory",
                                "selected": false
                            },
                            {
                                "label": "Cardiac disease",
                                "value": "Cardiac disease",
                                "selected": false
                            },
                            {
                                "label": "Cardiovascular",
                                "value": "Cardiovascular",
                                "selected": false
                            },
                            {
                                "label": "Hematological",
                                "value": "Hematological",
                                "selected": false
                            },
                            {
                                "label": "Lymphatic",
                                "value": "Lymphatic",
                                "selected": false
                            },
                            {
                                "label": "Neurological",
                                "value": "Neurological",
                                "selected": false
                            },
                            {
                                "label": "Psychiatric",
                                "value": "Psychiatric",
                                "selected": false
                            },
                            {
                                "label": "Gastrointestinal",
                                "value": "Gastrointestinal",
                                "selected": false
                            },
                            {
                                "label": "Genitourinary",
                                "value": "Genitourinary",
                                "selected": false
                            },
                            {
                                "label": "Weight gain",
                                "value": "Weight gain",
                                "selected": false
                            },
                            {
                                "label": "Weight loss",
                                "value": "Weight loss",
                                "selected": false
                            },
                            {
                                "label": "Other",
                                "value": "Other",
                                "selected": false
                            }
                        ]
                    },
                    {
                        "type": "radio-group",
                        "required": false,
                        "label": "<div>Do you have any medication allergies?</div>",
                        "inline": false,
                        "name": "allergies",
                        "access": false,
                        "other": false,
                        "values": [{
                                "label": "Yes",
                                "value": "yes",
                                "selected": false
                            },
                            {
                                "label": "No",
                                "value": "no",
                                "selected": false
                            },
                            {
                                "label": "Not Sure",
                                "value": "not sure",
                                "selected": false
                            }
                        ]
                    },
                    {
                        "type": "select",
                        "required": false,
                        "label": "Do you use any kind of tobacco or have you ever used them?",
                        "className": "form-control",
                        "name": "tobacco",
                        "access": false,
                        "multiple": false,
                        "values": [{
                                "label": "--Please Select--",
                                "value": "",
                                "selected": true
                            },
                            {
                                "label": "Yes",
                                "value": "Yes",
                                "selected": false
                            },
                            {
                                "label": "No",
                                "value": "No",
                                "selected": false
                            }
                        ]
                    },
                    {
                        "type": "radio-group",
                        "required": false,
                        "label": "<div>How often do you consume alcohol?</div>",
                        "inline": false,
                        "name": "alcohol",
                        "access": false,
                        "other": false,
                        "values": [{
                                "label": "Daily",
                                "value": "daily",
                                "selected": false
                            },
                            {
                                "label": "Weekly",
                                "value": "weekly",
                                "selected": false
                            },
                            {
                                "label": "Monthly",
                                "value": "monthly",
                                "selected": false
                            },
                            {
                                "label": "Occasionally",
                                "value": "occasionally",
                                "selected": false
                            },
                            {
                                "label": "Never",
                                "value": "never",
                                "selected": false
                            }
                        ]
                    }
                ]

            };
            var fields = [{
                    label: "Email",
                    type: "text",
                    subtype: "email",
                    icon: "✉"
                },
                {
                    icon: "📊",
                    "type": "select",
                    "required": false,
                    "label": "Category",
                    "className": "form-control",
                    "name": "category_id[]",
                    "access": false,
                    "multiple": true,
                    "values": []
                }
            ];

            const options = {
                defaultFields: isCustomTemplate ? templates.Custom : templates.default,
            };

            let formBuilder;

            templateSelect.addEventListener("change", function(e) {
                // Remove existing form builder elements
                $fbTemplate.empty();

                // Create a new instance with updated fields
                options.defaultFields = templates[e.target.value];
                formBuilder = $fbTemplate.formBuilder(options);
            });

            // Initialize the Form Builder instance based on the initial template
            formBuilder = $fbTemplate.formBuilder(options);

            // Select the "Custom" template if code is not empty
            if (isCustomTemplate) {
                templateSelect.value = 'Custom';
            }



            $(document).on('click', '.save-template', function() {
                var success = $('.alert.alert-success');
                var danger = $('.alert.alert-danger');

                // Get form data from the form builder
                var formData = formBuilder.formData;
                var module_name = $('.module_name').val();
                // Check if formData is the string representation of an empty array
                if (formData === "[]") {
                    danger.addClass('d-block');
                    success.removeClass('d-block');
                    $('.responsealert').text('Form data is empty. Please build your form.');
                    return;
                }
                if (module_name == '') {
                    danger.addClass('d-block');
                    success.removeClass('d-block');
                    $('.responsealert').text('Module Name is empty. Please enter your module name.');
                    return;
                }
                setInterval(() => {
                    danger.addClass('d-none');
                    success.addClass('d-none');
                }, 3000);
                // console.log(formData);
                // Make an AJAX request to your Laravel backend
                fetch('', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            '_token': '{{ csrf_token() }}',
                            code: formData,
                            name: module_name
                        }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === true) {
                            success.addClass('d-block');
                            danger.removeClass('d-block');
                            $('.responsealert').text(data.message);
                            //  setInterval(() => {
                            //     window.location.href = "{{ url('admin/module/list') }}";
                            //  }, 3000);

                        } else {
                            // $('.alert.alert-danger').show();
                            console.log(data);
                        }

                        // Handle the response as needed
                    })
                    .catch(error => console.error('Error:', error));
            });
        });
    </script>
@endsection
