<?php


use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CheckboxListType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CheckboxType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\DateTimeType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\DateType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\EmailType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\IconSelectType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\NumberType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\RadioType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\SelectType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\TextAreaType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\TextType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\ToggleButtonsType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\SectionType;

return [
    "custom_field_types" => [
        CheckboxType::class,
        DateTimeType::class,
        DateType::class,
        EmailType::class,
        NumberType::class,
        TextAreaType::class,
        TextType::class,
        SectionType::class,
        IconSelectType::class,
        SelectType::class,
        RadioType::class,
        CheckboxListType::class,
        ToggleButtonsType::class,
    ],

    "forms"=>[

    ],

    "disabled_general_field_types"=>[
        SectionType::class
    ],

    'view_modes' => [

    ],

    'default_column_count' => 8,

    "icons"=> [
        "Academic" => [
            "heroicon-o-academic-cap"
        ],
        "Adjustments" => [
            "heroicon-o-adjustments-horizontal",
            "heroicon-o-adjustments-vertical"
        ],
        "Archive" => [
            "heroicon-o-archive-box",
            "heroicon-o-archive-box-arrow-down",
            "heroicon-o-archive-box-x-mark"
        ],
        "Arrow" => [
            "heroicon-o-arrow-down",
            "heroicon-o-arrow-down-circle",
            "heroicon-o-arrow-down-left",
            "heroicon-o-arrow-down-on-square",
            "heroicon-o-arrow-down-on-square-stack",
            "heroicon-o-arrow-down-right",
            "heroicon-o-arrow-down-tray",
            "heroicon-o-arrow-left",
            "heroicon-o-arrow-left-circle",
            "heroicon-o-arrow-left-end-on-rectangle",
            "heroicon-o-arrow-left-start-on-rectangle",
            "heroicon-o-arrow-long-down",
            "heroicon-o-arrow-long-left",
            "heroicon-o-arrow-long-right",
            "heroicon-o-arrow-long-up",
            "heroicon-o-arrow-path",
            "heroicon-o-arrow-path-rounded-square",
            "heroicon-o-arrow-right",
            "heroicon-o-arrow-right-circle",
            "heroicon-o-arrow-right-end-on-rectangle",
            "heroicon-o-arrow-right-start-on-rectangle",
            "heroicon-o-arrow-top-right-on-square",
            "heroicon-o-arrow-trending-down",
            "heroicon-o-arrow-trending-up",
            "heroicon-o-arrow-up",
            "heroicon-o-arrow-up-circle",
            "heroicon-o-arrow-up-left",
            "heroicon-o-arrow-up-on-square",
            "heroicon-o-arrow-up-on-square-stack",
            "heroicon-o-arrow-up-right",
            "heroicon-o-arrow-up-tray",
            "heroicon-o-arrow-uturn-down",
            "heroicon-o-arrow-uturn-left",
            "heroicon-o-arrow-uturn-right",
            "heroicon-o-arrow-uturn-up"
        ],
        "Arrows" => [
            "heroicon-o-arrows-pointing-in",
            "heroicon-o-arrows-pointing-out",
            "heroicon-o-arrows-right-left",
            "heroicon-o-arrows-up-down"
        ],
        "Bars" => [
            "heroicon-o-bars-2",
            "heroicon-o-bars-3",
            "heroicon-o-bars-3-bottom-left",
            "heroicon-o-bars-3-bottom-right",
            "heroicon-o-bars-3-center-left",
            "heroicon-o-bars-4",
            "heroicon-o-bars-arrow-down",
            "heroicon-o-bars-arrow-up"
        ],
        "Battery" => [
            "heroicon-o-battery-0",
            "heroicon-o-battery-100",
            "heroicon-o-battery-50"
        ],
        "Bell" => [
            "heroicon-o-bell",
            "heroicon-o-bell-alert",
            "heroicon-o-bell-slash",
            "heroicon-o-bell-snooze",
            "heroicon-o-bolt",
            "heroicon-o-bolt-slash"
        ],
        "Book" => [
            "heroicon-o-book-open",
            "heroicon-o-bookmark",
            "heroicon-o-bookmark-slash",
            "heroicon-o-bookmark-square"
        ],
        "Briefcase" => [
            "heroicon-o-briefcase"
        ],
        "Building" => [
            "heroicon-o-building-library",
            "heroicon-o-building-office",
            "heroicon-o-building-office-2",
            "heroicon-o-building-storefront",
            "heroicon-o-home",
            "heroicon-o-home-modern"
        ],
        "Cake" => [
            "heroicon-o-cake"
        ],
        "Calculator" => [
            "heroicon-o-calculator"
        ],
        "Calendar" => [
            "heroicon-o-calendar",
            "heroicon-o-calendar-days"
        ],
        "Chart" => [
            "heroicon-o-chart-bar",
            "heroicon-o-chart-bar-square",
            "heroicon-o-chart-pie"
        ],
        "Chat" => [
            "heroicon-o-chat-bubble-bottom-center",
            "heroicon-o-chat-bubble-bottom-center-text",
            "heroicon-o-chat-bubble-left",
            "heroicon-o-chat-bubble-left-ellipsis",
            "heroicon-o-chat-bubble-left-right",
            "heroicon-o-chat-bubble-oval-left",
            "heroicon-o-chat-bubble-oval-left-ellipsis"
        ],
        "Check" => [
            "heroicon-o-check",
            "heroicon-o-check-badge",
            "heroicon-o-check-circle"
        ],
        "Chevron" => [
            "heroicon-o-chevron-double-down",
            "heroicon-o-chevron-double-left",
            "heroicon-o-chevron-double-right",
            "heroicon-o-chevron-double-up",
            "heroicon-o-chevron-down",
            "heroicon-o-chevron-left",
            "heroicon-o-chevron-right",
            "heroicon-o-chevron-up",
            "heroicon-o-chevron-up-down"
        ],
        "Clipboard" => [
            "heroicon-o-clipboard",
            "heroicon-o-clipboard-document",
            "heroicon-o-clipboard-document-check",
            "heroicon-o-clipboard-document-list"
        ],
        "Cloud" => [
            "heroicon-o-cloud",
            "heroicon-o-cloud-arrow-down",
            "heroicon-o-cloud-arrow-up"
        ],
        "Cog" => [
            "heroicon-o-cog",
            "heroicon-o-cog-6-tooth",
            "heroicon-o-cog-8-tooth",
            "heroicon-o-wrench",
            "heroicon-o-wrench-screwdriver"
        ],
        "Command" => [
            "heroicon-o-command-line",
            "heroicon-o-bug-ant",
            "heroicon-o-computer-desktop",
            "heroicon-o-cpu-chip",
            "heroicon-o-code-bracket",
            "heroicon-o-code-bracket-square",
            "heroicon-o-qr-code",
            "heroicon-o-link",
            "heroicon-o-wifi",
            "heroicon-o-window",
            "heroicon-o-tv",
            "heroicon-o-server",
            "heroicon-o-server-stack",
            "heroicon-o-printer",
            "heroicon-o-circle-stack",
            "heroicon-o-cursor-arrow-rays",
            "heroicon-o-cursor-arrow-ripple",
            "heroicon-o-device-phone-mobile",
            "heroicon-o-device-tablet",
            "heroicon-o-funnel",
            "heroicon-o-finger-print",
            "heroicon-o-gif"
        ],
        "Credit Card" => [
            "heroicon-o-credit-card",
            "heroicon-o-identification"
        ],
        "Cube" => [
            "heroicon-o-cube",
            "heroicon-o-cube-transparent"
        ],
        "Currency" => [
            "heroicon-o-currency-bangladeshi",
            "heroicon-o-currency-dollar",
            "heroicon-o-currency-euro",
            "heroicon-o-currency-pound",
            "heroicon-o-currency-rupee",
            "heroicon-o-currency-yen"
        ],
        "Document" => [
            "heroicon-o-document",
            "heroicon-o-document-arrow-down",
            "heroicon-o-document-arrow-up",
            "heroicon-o-document-chart-bar",
            "heroicon-o-document-check",
            "heroicon-o-document-duplicate",
            "heroicon-o-document-magnifying-glass",
            "heroicon-o-document-minus",
            "heroicon-o-document-plus",
            "heroicon-o-document-text"
        ],
        "Ellipsis" => [
            "heroicon-o-ellipsis-horizontal",
            "heroicon-o-ellipsis-horizontal-circle",
            "heroicon-o-ellipsis-vertical"
        ],
        "Envelope" => [
            "heroicon-o-envelope",
            "heroicon-o-envelope-open"
        ],
        "Exclamation" => [
            "heroicon-o-exclamation-circle",
            "heroicon-o-exclamation-triangle"
        ],
        "Eye" => [
            "heroicon-o-eye",
            "heroicon-o-eye-dropper",
            "heroicon-o-eye-slash"
        ],
        "Film" => [
            "heroicon-o-camera",
            "heroicon-o-film",
            "heroicon-o-forward",
            "heroicon-o-video-camera",
            "heroicon-o-video-camera-slash",
            "heroicon-o-view-columns",
            "heroicon-o-viewfinder-circle",
            "heroicon-o-musical-note",
            "heroicon-o-pause",
            "heroicon-o-pause-circle",
            "heroicon-o-photo",
        ],
        "Fire" => [
            "heroicon-o-face-frown",
            "heroicon-o-face-smile",
            "heroicon-o-heart",
            "heroicon-o-fire",
            "heroicon-o-star",
            "heroicon-o-sun",
            "heroicon-o-flag",
            "heroicon-o-rocket-launch",
        ],
        "Folder" => [
            "heroicon-o-folder",
            "heroicon-o-folder-arrow-down",
            "heroicon-o-folder-minus",
            "heroicon-o-folder-open",
            "heroicon-o-folder-plus"
        ],
        "Gift" => [
            "heroicon-o-gift",
            "heroicon-o-gift-top"
        ],
        "Globe" => [
            "heroicon-o-globe-alt",
            "heroicon-o-globe-americas",
            "heroicon-o-globe-asia-australia",
            "heroicon-o-globe-europe-africa"
        ],
        "Hand" => [
            "heroicon-o-hand-raised",
            "heroicon-o-hand-thumb-down",
            "heroicon-o-hand-thumb-up"
        ],
        "Hashtag" => [
            "heroicon-o-hashtag"
        ],
        "Inbox" => [
            "heroicon-o-inbox",
            "heroicon-o-inbox-arrow-down",
            "heroicon-o-inbox-stack"
        ],
        "Information" => [
            "heroicon-o-information-circle"
        ],
        "Key" => [
            "heroicon-o-key"
        ],
        "Language" => [
            "heroicon-o-language"
        ],
        "Lifebuoy" => [
            "heroicon-o-lifebuoy"
        ],
        "Light Bulb" => [
            "heroicon-o-light-bulb"
        ],
        "List" => [
            "heroicon-o-list-bullet"
        ],
        "Lock" => [
            "heroicon-o-lock-closed",
            "heroicon-o-lock-open"
        ],
        "Magnifying Glass" => [
            "heroicon-o-magnifying-glass",
            "heroicon-o-magnifying-glass-circle",
            "heroicon-o-magnifying-glass-minus",
            "heroicon-o-magnifying-glass-plus"
        ],
        "Map" => [
            "heroicon-o-map",
            "heroicon-o-map-pin"
        ],
        "Megaphone" => [
            "heroicon-o-megaphone"
        ],
        "Microphone" => [
            "heroicon-o-microphone"
        ],
        "Minus" => [
            "heroicon-o-minus",
            "heroicon-o-minus-circle"
        ],
        "Moon" => [
            "heroicon-o-moon"
        ],
        "Newspaper" => [
            "heroicon-o-newspaper"
        ],
        "No Symbol" => [
            "heroicon-o-no-symbol"
        ],
        "Paint Brush" => [
            "heroicon-o-paint-brush"
        ],
        "Paper" => [
            "heroicon-o-paper-airplane",
            "heroicon-o-paper-clip"
        ],
        "Pencil" => [
            "heroicon-o-pencil",
            "heroicon-o-pencil-square"
        ],
        "Phone" => [
            "heroicon-o-phone",
            "heroicon-o-phone-arrow-down-left",
            "heroicon-o-phone-arrow-up-right",
            "heroicon-o-phone-x-mark"
        ],
        "Play" => [
            "heroicon-o-play",
            "heroicon-o-play-circle",
            "heroicon-o-play-pause"
        ],
        "Plus" => [
            "heroicon-o-plus",
            "heroicon-o-plus-circle"
        ],
        "Power" => [
            "heroicon-o-power"
        ],
        "Presentation" => [
            "heroicon-o-presentation-chart-bar",
            "heroicon-o-presentation-chart-line"
        ],
        "Puzzle Piece" => [
            "heroicon-o-puzzle-piece"
        ],
        "Question Mark" => [
            "heroicon-o-question-mark-circle"
        ],
        "Queue List" => [
            "heroicon-o-queue-list"
        ],
        "Radio" => [
            "heroicon-o-radio"
        ],
        "Receipt" => [
            "heroicon-o-receipt-percent",
            "heroicon-o-receipt-refund"
        ],
        "Rectangle" => [
            "heroicon-o-rectangle-group",
            "heroicon-o-rectangle-stack"
        ],
        "RSS" => [
            "heroicon-o-rss"
        ],
        "Scale" => [
            "heroicon-o-scale"
        ],
        "Scissors" => [
            "heroicon-o-scissors"
        ],
        "Share" => [
            "heroicon-o-share"
        ],
        "Shield" => [
            "heroicon-o-shield-check",
            "heroicon-o-shield-exclamation"
        ],
        "Shopping" => [
            "heroicon-o-shopping-bag",
            "heroicon-o-shopping-cart"
        ],
        "Signal" => [
            "heroicon-o-signal",
            "heroicon-o-signal-slash"
        ],
        "Speaker" => [
            "heroicon-o-speaker-wave",
            "heroicon-o-speaker-x-mark"
        ],
        "Sparkles" => [
            "heroicon-o-sparkles"
        ],
        "Square" => [
            "heroicon-o-square-2-stack",
            "heroicon-o-square-3-stack-3d",
            "heroicon-o-squares-2x2",
            "heroicon-o-squares-plus"
        ],
        "Stop" => [
            "heroicon-o-stop",
            "heroicon-o-stop-circle"
        ],
        "Swatch" => [
            "heroicon-o-swatch"
        ],
        "Table" => [
            "heroicon-o-table-cells"
        ],
        "Tag" => [
            "heroicon-o-tag",
            "heroicon-o-ticket"
        ],
        "Trash" => [
            "heroicon-o-trash"
        ],
        "Trophy" => [
            "heroicon-o-trophy"
        ],
        "Truck" => [
            "heroicon-o-truck"
        ],
        "User" => [
            "heroicon-o-user",
            "heroicon-o-user-circle",
            "heroicon-o-user-group",
            "heroicon-o-user-minus",
            "heroicon-o-user-plus",
            "heroicon-o-users"
        ],
        "Variable" => [
            "heroicon-o-variable"
        ],
        "Wallet" => [
            "heroicon-o-wallet"
        ],
        "X" => [
            "heroicon-o-x-circle",
            "heroicon-o-x-mark"
        ]
    ]


];
