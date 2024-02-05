<?php


use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CheckboxType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\DateTimeType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\DateType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\EmailType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\IconSelectType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\NumberType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\SelectType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\TextAreaType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\TextType;
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
            "heroicon-s-academic-cap"
        ],
        "Adjustments" => [
            "heroicon-s-adjustments-horizontal",
            "heroicon-s-adjustments-vertical"
        ],
        "Archive" => [
            "heroicon-s-archive-box",
            "heroicon-s-archive-box-arrow-down",
            "heroicon-s-archive-box-x-mark"
        ],
        "Arrow" => [
            "heroicon-s-arrow-down",
            "heroicon-s-arrow-down-circle",
            "heroicon-s-arrow-down-left",
            "heroicon-s-arrow-down-on-square",
            "heroicon-s-arrow-down-on-square-stack",
            "heroicon-s-arrow-down-right",
            "heroicon-s-arrow-down-tray",
            "heroicon-s-arrow-left",
            "heroicon-s-arrow-left-circle",
            "heroicon-s-arrow-left-end-on-rectangle",
            "heroicon-s-arrow-left-start-on-rectangle",
            "heroicon-s-arrow-long-down",
            "heroicon-s-arrow-long-left",
            "heroicon-s-arrow-long-right",
            "heroicon-s-arrow-long-up",
            "heroicon-s-arrow-path",
            "heroicon-s-arrow-path-rounded-square",
            "heroicon-s-arrow-right",
            "heroicon-s-arrow-right-circle",
            "heroicon-s-arrow-right-end-on-rectangle",
            "heroicon-s-arrow-right-start-on-rectangle",
            "heroicon-s-arrow-top-right-on-square",
            "heroicon-s-arrow-trending-down",
            "heroicon-s-arrow-trending-up",
            "heroicon-s-arrow-up",
            "heroicon-s-arrow-up-circle",
            "heroicon-s-arrow-up-left",
            "heroicon-s-arrow-up-on-square",
            "heroicon-s-arrow-up-on-square-stack",
            "heroicon-s-arrow-up-right",
            "heroicon-s-arrow-up-tray",
            "heroicon-s-arrow-uturn-down",
            "heroicon-s-arrow-uturn-left",
            "heroicon-s-arrow-uturn-right",
            "heroicon-s-arrow-uturn-up"
        ],
        "Arrows" => [
            "heroicon-s-arrows-pointing-in",
            "heroicon-s-arrows-pointing-out",
            "heroicon-s-arrows-right-left",
            "heroicon-s-arrows-up-down"
        ],
        "Bars" => [
            "heroicon-s-bars-2",
            "heroicon-s-bars-3",
            "heroicon-s-bars-3-bottom-left",
            "heroicon-s-bars-3-bottom-right",
            "heroicon-s-bars-3-center-left",
            "heroicon-s-bars-4",
            "heroicon-s-bars-arrow-down",
            "heroicon-s-bars-arrow-up"
        ],
        "Battery" => [
            "heroicon-s-battery-0",
            "heroicon-s-battery-100",
            "heroicon-s-battery-50"
        ],
        "Bell" => [
            "heroicon-s-bell",
            "heroicon-s-bell-alert",
            "heroicon-s-bell-slash",
            "heroicon-s-bell-snooze",
            "heroicon-s-bolt",
            "heroicon-s-bolt-slash"
        ],
        "Book" => [
            "heroicon-s-book-open",
            "heroicon-s-bookmark",
            "heroicon-s-bookmark-slash",
            "heroicon-s-bookmark-square"
        ],
        "Briefcase" => [
            "heroicon-s-briefcase"
        ],
        "Building" => [
            "heroicon-s-building-library",
            "heroicon-s-building-office",
            "heroicon-s-building-office-2",
            "heroicon-s-building-storefront",
            "heroicon-s-home",
            "heroicon-s-home-modern"
        ],
        "Cake" => [
            "heroicon-s-cake"
        ],
        "Calculator" => [
            "heroicon-s-calculator"
        ],
        "Calendar" => [
            "heroicon-s-calendar",
            "heroicon-s-calendar-days"
        ],
        "Chart" => [
            "heroicon-s-chart-bar",
            "heroicon-s-chart-bar-square",
            "heroicon-s-chart-pie"
        ],
        "Chat" => [
            "heroicon-s-chat-bubble-bottom-center",
            "heroicon-s-chat-bubble-bottom-center-text",
            "heroicon-s-chat-bubble-left",
            "heroicon-s-chat-bubble-left-ellipsis",
            "heroicon-s-chat-bubble-left-right",
            "heroicon-s-chat-bubble-oval-left",
            "heroicon-s-chat-bubble-oval-left-ellipsis"
        ],
        "Check" => [
            "heroicon-s-check",
            "heroicon-s-check-badge",
            "heroicon-s-check-circle"
        ],
        "Chevron" => [
            "heroicon-s-chevron-double-down",
            "heroicon-s-chevron-double-left",
            "heroicon-s-chevron-double-right",
            "heroicon-s-chevron-double-up",
            "heroicon-s-chevron-down",
            "heroicon-s-chevron-left",
            "heroicon-s-chevron-right",
            "heroicon-s-chevron-up",
            "heroicon-s-chevron-up-down"
        ],
        "Clipboard" => [
            "heroicon-s-clipboard",
            "heroicon-s-clipboard-document",
            "heroicon-s-clipboard-document-check",
            "heroicon-s-clipboard-document-list"
        ],
        "Cloud" => [
            "heroicon-s-cloud",
            "heroicon-s-cloud-arrow-down",
            "heroicon-s-cloud-arrow-up"
        ],
        "Cog" => [
            "heroicon-s-cog",
            "heroicon-s-cog-6-tooth",
            "heroicon-s-cog-8-tooth",
            "heroicon-s-wrench",
            "heroicon-s-wrench-screwdriver"
        ],
        "Command" => [
            "heroicon-s-command-line",
            "heroicon-s-bug-ant",
            "heroicon-s-computer-desktop",
            "heroicon-s-cpu-chip",
            "heroicon-s-code-bracket",
            "heroicon-s-code-bracket-square",
            "heroicon-s-qr-code",
            "heroicon-s-link",
            "heroicon-s-wifi",
            "heroicon-s-window",
            "heroicon-s-tv",
            "heroicon-s-server",
            "heroicon-s-server-stack",
            "heroicon-s-printer",
            "heroicon-s-circle-stack",
            "heroicon-s-cursor-arrow-rays",
            "heroicon-s-cursor-arrow-ripple",
            "heroicon-s-device-phone-mobile",
            "heroicon-s-device-tablet",
            "heroicon-s-funnel",
            "heroicon-s-finger-print",
            "heroicon-s-gif"
        ],
        "Credit Card" => [
            "heroicon-s-credit-card",
            "heroicon-s-identification"
        ],
        "Cube" => [
            "heroicon-s-cube",
            "heroicon-s-cube-transparent"
        ],
        "Currency" => [
            "heroicon-s-currency-bangladeshi",
            "heroicon-s-currency-dollar",
            "heroicon-s-currency-euro",
            "heroicon-s-currency-pound",
            "heroicon-s-currency-rupee",
            "heroicon-s-currency-yen"
        ],
        "Document" => [
            "heroicon-s-document",
            "heroicon-s-document-arrow-down",
            "heroicon-s-document-arrow-up",
            "heroicon-s-document-chart-bar",
            "heroicon-s-document-check",
            "heroicon-s-document-duplicate",
            "heroicon-s-document-magnifying-glass",
            "heroicon-s-document-minus",
            "heroicon-s-document-plus",
            "heroicon-s-document-text"
        ],
        "Ellipsis" => [
            "heroicon-s-ellipsis-horizontal",
            "heroicon-s-ellipsis-horizontal-circle",
            "heroicon-s-ellipsis-vertical"
        ],
        "Envelope" => [
            "heroicon-s-envelope",
            "heroicon-s-envelope-open"
        ],
        "Exclamation" => [
            "heroicon-s-exclamation-circle",
            "heroicon-s-exclamation-triangle"
        ],
        "Eye" => [
            "heroicon-s-eye",
            "heroicon-s-eye-dropper",
            "heroicon-s-eye-slash"
        ],
        "Film" => [
            "heroicon-s-camera",
            "heroicon-s-film",
            "heroicon-s-forward",
            "heroicon-s-video-camera",
            "heroicon-s-video-camera-slash",
            "heroicon-s-view-columns",
            "heroicon-s-viewfinder-circle",
            "heroicon-s-musical-note",
            "heroicon-s-pause",
            "heroicon-s-pause-circle",
            "heroicon-s-photo",
        ],
        "Fire" => [
            "heroicon-s-face-frown",
            "heroicon-s-face-smile",
            "heroicon-s-heart",
            "heroicon-s-fire",
            "heroicon-s-star",
            "heroicon-s-sun",
            "heroicon-s-flag",
            "heroicon-s-rocket-launch",
        ],
        "Folder" => [
            "heroicon-s-folder",
            "heroicon-s-folder-arrow-down",
            "heroicon-s-folder-minus",
            "heroicon-s-folder-open",
            "heroicon-s-folder-plus"
        ],
        "Gift" => [
            "heroicon-s-gift",
            "heroicon-s-gift-top"
        ],
        "Globe" => [
            "heroicon-s-globe-alt",
            "heroicon-s-globe-americas",
            "heroicon-s-globe-asia-australia",
            "heroicon-s-globe-europe-africa"
        ],
        "Hand" => [
            "heroicon-s-hand-raised",
            "heroicon-s-hand-thumb-down",
            "heroicon-s-hand-thumb-up"
        ],
        "Hashtag" => [
            "heroicon-s-hashtag"
        ],
        "Inbox" => [
            "heroicon-s-inbox",
            "heroicon-s-inbox-arrow-down",
            "heroicon-s-inbox-stack"
        ],
        "Information" => [
            "heroicon-s-information-circle"
        ],
        "Key" => [
            "heroicon-s-key"
        ],
        "Language" => [
            "heroicon-s-language"
        ],
        "Lifebuoy" => [
            "heroicon-s-lifebuoy"
        ],
        "Light Bulb" => [
            "heroicon-s-light-bulb"
        ],
        "List" => [
            "heroicon-s-list-bullet"
        ],
        "Lock" => [
            "heroicon-s-lock-closed",
            "heroicon-s-lock-open"
        ],
        "Magnifying Glass" => [
            "heroicon-s-magnifying-glass",
            "heroicon-s-magnifying-glass-circle",
            "heroicon-s-magnifying-glass-minus",
            "heroicon-s-magnifying-glass-plus"
        ],
        "Map" => [
            "heroicon-s-map",
            "heroicon-s-map-pin"
        ],
        "Megaphone" => [
            "heroicon-s-megaphone"
        ],
        "Microphone" => [
            "heroicon-s-microphone"
        ],
        "Minus" => [
            "heroicon-s-minus",
            "heroicon-s-minus-circle"
        ],
        "Moon" => [
            "heroicon-s-moon"
        ],
        "Newspaper" => [
            "heroicon-s-newspaper"
        ],
        "No Symbol" => [
            "heroicon-s-no-symbol"
        ],
        "Paint Brush" => [
            "heroicon-s-paint-brush"
        ],
        "Paper" => [
            "heroicon-s-paper-airplane",
            "heroicon-s-paper-clip"
        ],
        "Pencil" => [
            "heroicon-s-pencil",
            "heroicon-s-pencil-square"
        ],
        "Phone" => [
            "heroicon-s-phone",
            "heroicon-s-phone-arrow-down-left",
            "heroicon-s-phone-arrow-up-right",
            "heroicon-s-phone-x-mark"
        ],
        "Play" => [
            "heroicon-s-play",
            "heroicon-s-play-circle",
            "heroicon-s-play-pause"
        ],
        "Plus" => [
            "heroicon-s-plus",
            "heroicon-s-plus-circle"
        ],
        "Power" => [
            "heroicon-s-power"
        ],
        "Presentation" => [
            "heroicon-s-presentation-chart-bar",
            "heroicon-s-presentation-chart-line"
        ],
        "Puzzle Piece" => [
            "heroicon-s-puzzle-piece"
        ],
        "Question Mark" => [
            "heroicon-s-question-mark-circle"
        ],
        "Queue List" => [
            "heroicon-s-queue-list"
        ],
        "Radio" => [
            "heroicon-s-radio"
        ],
        "Receipt" => [
            "heroicon-s-receipt-percent",
            "heroicon-s-receipt-refund"
        ],
        "Rectangle" => [
            "heroicon-s-rectangle-group",
            "heroicon-s-rectangle-stack"
        ],
        "RSS" => [
            "heroicon-s-rss"
        ],
        "Scale" => [
            "heroicon-s-scale"
        ],
        "Scissors" => [
            "heroicon-s-scissors"
        ],
        "Share" => [
            "heroicon-s-share"
        ],
        "Shield" => [
            "heroicon-s-shield-check",
            "heroicon-s-shield-exclamation"
        ],
        "Shopping" => [
            "heroicon-s-shopping-bag",
            "heroicon-s-shopping-cart"
        ],
        "Signal" => [
            "heroicon-s-signal",
            "heroicon-s-signal-slash"
        ],
        "Speaker" => [
            "heroicon-s-speaker-wave",
            "heroicon-s-speaker-x-mark"
        ],
        "Sparkles" => [
            "heroicon-s-sparkles"
        ],
        "Square" => [
            "heroicon-s-square-2-stack",
            "heroicon-s-square-3-stack-3d",
            "heroicon-s-squares-2x2",
            "heroicon-s-squares-plus"
        ],
        "Stop" => [
            "heroicon-s-stop",
            "heroicon-s-stop-circle"
        ],
        "Swatch" => [
            "heroicon-s-swatch"
        ],
        "Table" => [
            "heroicon-s-table-cells"
        ],
        "Tag" => [
            "heroicon-s-tag",
            "heroicon-s-ticket"
        ],
        "Trash" => [
            "heroicon-s-trash"
        ],
        "Trophy" => [
            "heroicon-s-trophy"
        ],
        "Truck" => [
            "heroicon-s-truck"
        ],
        "User" => [
            "heroicon-s-user",
            "heroicon-s-user-circle",
            "heroicon-s-user-group",
            "heroicon-s-user-minus",
            "heroicon-s-user-plus",
            "heroicon-s-users"
        ],
        "Variable" => [
            "heroicon-s-variable"
        ],
        "Wallet" => [
            "heroicon-s-wallet"
        ],
        "X" => [
            "heroicon-s-x-circle",
            "heroicon-s-x-mark"
        ]
    ]


];
