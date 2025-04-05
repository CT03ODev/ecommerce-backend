<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Profiles
    |--------------------------------------------------------------------------
    |
    | You can add as many as you want of profiles to use it in your application.
    |
    */

    'profiles' => [

        'default' => [
            'plugins' => 'preview importcss searchreplace autolink autosave save directionality visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons code',
            'toolbar' => 'undo redo | fontfamily fontsize | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor casechange removeformat | pagebreak | charmap emoticons | fullscreen  preview save | insertfile image media template link anchor codesample | a11ycheck ltr rtl | showcomments addcomment',
            'upload_directory' => null,
            'paste_data_images' => true,
            'automatic_uploads' => true,
            'block_unsupported_drop' => false, 
            'custom_configs' => [
                'menubar' => 'file edit view insert format tools table tc help',
                'content_style' => 'body{background:#fff;color:#22262a;font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,Noto Sans,sans-serif,Apple Color Emoji,Segoe UI Emoji,Segoe UI Symbol,Noto Color Emoji;font-size:1rem;line-height:1.3em}h1,h2,h3,h4{font-weight:700}h1{font-size:1.857rem}h2{font-size:1.571rem}h3{font-size:1.286rem}a:link,a:visited{color:#1b57b1;font-weight:400;text-decoration:none}a:hover{color:#00c;font-weight:400;text-decoration:underline}div.caption{padding:0 10px}div.caption img{border:1px solid #ccc}div.caption p{color:#666;font-size:.9em;text-align:center}hr#system-readmore{border:1px dashed red;color:red}hr.system-pagebreak{border:1px dashed grey;color:grey}span[lang]{border:1px dashed #bbb;padding:2px}span[lang]:after{color:red;content:attr(lang);font-size:smaller;vertical-align:super}',
                'toolbar_mode' => 'wrap',
                'extended_valid_elements' => "hr[id|title|alt|class|width|size|noshade]",
                'fontsize_formats' => "8px 10px 12px 14px 18px 24px 36px",
                'quickbars_selection_toolbar' => 'bold italic | quicklink h2 h3 h4 blockquote',
                'entity_encoding' => 'raw',
                'remove_script_host' => false,
                'relative_urls' => false,
                'remove_script_host' => false,
                'convert_urls' => true,
                'image_caption' => true,
                'image_advtab' => true,
                'image_title' => true,
                'rel_list' => [
                    ["title" => "None", "value" => ""],
                    ["title" => "Alternate", "value" => "alternate"],
                    ["title" => "Author", "value" => "author"],
                    ["title" => "Bookmark", "value" => "bookmark"],
                    ["title" => "Help", "value" => "help"],
                    ["title" => "License", "value" => "license"],
                    ["title" => "Lightbox", "value" => "lightbox"],
                    ["title" => "Next", "value" => "next"],
                    ["title" => "No Follow", "value" => "nofollow"],
                    ["title" => "No Referrer", "value" => "noreferrer"],
                    ["title" => "Prefetch", "value" => "prefetch"],
                    ["title" => "Prev", "value" => "prev"],
                    ["title" => "Search", "value" => "search"],
                    ["title" => "Tag", "value" => "tag"],
                ],
                'target_list' => [
                    ['title' => 'None', 'value' => ''],
                    ['title' => 'Same page', 'value' => '_self'],
                    ['title' => 'New page', 'value' => '_blank'],
                    ['title' => 'Parent frame', 'value' => '_parent']
                ],
                'verify_html' => true
            ]
        ],

        'simple' => [
            'plugins' => 'autoresize directionality emoticons link wordcount',
            'toolbar' => 'removeformat | bold italic | rtl ltr | link emoticons',
            'upload_directory' => null,
        ],

        'template' => [
            'plugins' => 'autoresize template',
            'toolbar' => 'template',
            'upload_directory' => null,
        ],
        /*
        |--------------------------------------------------------------------------
        | Custom Configs
        |--------------------------------------------------------------------------
        |
        | If you want to add custom configurations to directly tinymce
        | You can use custom_configs key as an array
        |
        */

        /*
          'default' => [
            'plugins' => 'advlist autoresize codesample directionality emoticons fullscreen hr image imagetools link lists media table toc wordcount',
            'toolbar' => 'undo redo removeformat | formatselect fontsizeselect | bold italic | rtl ltr | alignjustify alignright aligncenter alignleft | numlist bullist | forecolor backcolor | blockquote table toc hr | image link media codesample emoticons | wordcount fullscreen',
            'custom_configs' => [
                'allow_html_in_named_anchor' => true,
                'link_default_target' => '_blank',
                'codesample_global_prismjs' => true,
                'image_advtab' => true,
                'image_class_list' => [
                  [
                    'title' => 'None',
                    'value' => '',
                  ],
                  [
                    'title' => 'Fluid',
                    'value' => 'img-fluid',
                  ],
              ],
            ]
        ],
        */

    ],

    /*
    |--------------------------------------------------------------------------
    | Templates
    |--------------------------------------------------------------------------
    |
    | You can add as many as you want of templates to use it in your application.
    |
    | https://www.tiny.cloud/docs/plugins/opensource/template/#templates
    |
    | ex: TinyEditor::make('content')->profiles('template')->template('example')
    */

    'templates' => [

        'example' => [
            // content
            ['title' => 'Some title 1', 'description' => 'Some desc 1', 'content' => 'My content'],
            // url
            ['title' => 'Some title 2', 'description' => 'Some desc 2', 'url' => 'http://localhost'],
        ],

    ],
];
