.bg-gray {
            background-color: #ddd;
        }

        .bg-white {
            background-color: #fff;
        }

        .wrapper {
            max-width: 40rem;
            margin: auto;
            padding: 2rem 0;
        }

        .flex-gap-2 {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .content {
            padding: 0.5rem;
            border-radius: 4px;
        }

        .main {
            border-left: 3px dashed black;
            border-right: 3px dashed black;
            display: grid;
            grid-template-columns: repeat(5, 1fr);
        }

        .col-span-main {
            grid-column: span 4 / span 4;
        }

        .col-span-side {
            grid-column: span 1 / span 1;
        }

        .title {
            font-size: 2rem;
            font-weight: 500;
        }

        .collect-amount {
            padding: 0.5rem;
            font-weight: 400;
            font-size: 0.8rem;
        }

        /* ! tailwindcss v3.2.4 | MIT License | https://tailwindcss.com */
        *,
        ::after,
        ::before {
            box-sizing: border-box;
            border-width: 0;
            border-style: solid;
            border-color: #e5e7eb
        }

        ::after,
        ::before {
            --tw-content: ''
        }

        html {
            line-height: 1.5;
            -webkit-text-size-adjust: 100%;
            -moz-tab-size: 4;
            tab-size: 4;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            font-feature-settings: normal
        }

        body {
            margin: 0;
            line-height: inherit
        }

        hr {
            height: 0;
            color: inherit;
            border-top-width: 1px
        }

        abbr:where([title]) {
            -webkit-text-decoration: underline dotted;
            text-decoration: underline dotted
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-size: inherit;
            font-weight: inherit
        }

        a {
            color: inherit;
            text-decoration: inherit
        }

        b,
        strong {
            font-weight: bolder
        }

        code,
        kbd,
        pre,
        samp {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            font-size: 1em
        }

        small {
            font-size: 80%
        }

        sub,
        sup {
            font-size: 75%;
            line-height: 0;
            position: relative;
            vertical-align: baseline
        }

        sub {
            bottom: -.25em
        }

        sup {
            top: -.5em
        }

        table {
            text-indent: 0;
            border-color: inherit;
            border-collapse: collapse
        }

        button,
        input,
        optgroup,
        select,
        textarea {
            font-family: inherit;
            font-size: 100%;
            font-weight: inherit;
            line-height: inherit;
            color: inherit;
            margin: 0;
            padding: 0
        }

        button,
        select {
            text-transform: none
        }

        [type=button],
        [type=reset],
        [type=submit],
        button {
            -webkit-appearance: button;
            background-color: transparent;
            background-image: none
        }

        :-moz-focusring {
            outline: auto
        }

        :-moz-ui-invalid {
            box-shadow: none
        }

        progress {
            vertical-align: baseline
        }

        ::-webkit-inner-spin-button,
        ::-webkit-outer-spin-button {
            height: auto
        }

        [type=search] {
            -webkit-appearance: textfield;
            outline-offset: -2px
        }

        ::-webkit-search-decoration {
            -webkit-appearance: none
        }

        ::-webkit-file-upload-button {
            -webkit-appearance: button;
            font: inherit
        }

        summary {
            display: list-item
        }

        blockquote,
        dd,
        dl,
        figure,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        hr,
        p,
        pre {
            margin: 0
        }

        fieldset {
            margin: 0;
            padding: 0
        }

        legend {
            padding: 0
        }

        menu,
        ol,
        ul {
            list-style: none;
            margin: 0;
            padding: 0
        }

        textarea {
            resize: vertical
        }

        input::placeholder,
        textarea::placeholder {
            opacity: 1;
            color: #9ca3af
        }

        [role=button],
        button {
            cursor: pointer
        }

        :disabled {
            cursor: default
        }

        audio,
        canvas,
        embed,
        iframe,
        img,
        object,
        svg,
        video {
            display: block;
            vertical-align: middle
        }

        img,
        video {
            max-width: 100%;
            height: auto
        }

        [hidden] {
            display: none
        }

        *,
        ::before,
        ::after {
            --tw-border-spacing-x: 0;
            --tw-border-spacing-y: 0;
            --tw-translate-x: 0;
            --tw-translate-y: 0;
            --tw-rotate: 0;
            --tw-skew-x: 0;
            --tw-skew-y: 0;
            --tw-scale-x: 1;
            --tw-scale-y: 1;
            --tw-pan-x: ;
            --tw-pan-y: ;
            --tw-pinch-zoom: ;
            --tw-scroll-snap-strictness: proximity;
            --tw-ordinal: ;
            --tw-slashed-zero: ;
            --tw-numeric-figure: ;
            --tw-numeric-spacing: ;
            --tw-numeric-fraction: ;
            --tw-ring-inset: ;
            --tw-ring-offset-width: 0px;
            --tw-ring-offset-color: #fff;
            --tw-ring-color: rgb(59 130 246 / 0.5);
            --tw-ring-offset-shadow: 0 0 #0000;
            --tw-ring-shadow: 0 0 #0000;
            --tw-shadow: 0 0 #0000;
            --tw-shadow-colored: 0 0 #0000;
            --tw-blur: ;
            --tw-brightness: ;
            --tw-contrast: ;
            --tw-grayscale: ;
            --tw-hue-rotate: ;
            --tw-invert: ;
            --tw-saturate: ;
            --tw-sepia: ;
            --tw-drop-shadow: ;
            --tw-backdrop-blur: ;
            --tw-backdrop-brightness: ;
            --tw-backdrop-contrast: ;
            --tw-backdrop-grayscale: ;
            --tw-backdrop-hue-rotate: ;
            --tw-backdrop-invert: ;
            --tw-backdrop-opacity: ;
            --tw-backdrop-saturate: ;
            --tw-backdrop-sepia:
        }

        ::-webkit-backdrop {
            --tw-border-spacing-x: 0;
            --tw-border-spacing-y: 0;
            --tw-translate-x: 0;
            --tw-translate-y: 0;
            --tw-rotate: 0;
            --tw-skew-x: 0;
            --tw-skew-y: 0;
            --tw-scale-x: 1;
            --tw-scale-y: 1;
            --tw-pan-x: ;
            --tw-pan-y: ;
            --tw-pinch-zoom: ;
            --tw-scroll-snap-strictness: proximity;
            --tw-ordinal: ;
            --tw-slashed-zero: ;
            --tw-numeric-figure: ;
            --tw-numeric-spacing: ;
            --tw-numeric-fraction: ;
            --tw-ring-inset: ;
            --tw-ring-offset-width: 0px;
            --tw-ring-offset-color: #fff;
            --tw-ring-color: rgb(59 130 246 / 0.5);
            --tw-ring-offset-shadow: 0 0 #0000;
            --tw-ring-shadow: 0 0 #0000;
            --tw-shadow: 0 0 #0000;
            --tw-shadow-colored: 0 0 #0000;
            --tw-blur: ;
            --tw-brightness: ;
            --tw-contrast: ;
            --tw-grayscale: ;
            --tw-hue-rotate: ;
            --tw-invert: ;
            --tw-saturate: ;
            --tw-sepia: ;
            --tw-drop-shadow: ;
            --tw-backdrop-blur: ;
            --tw-backdrop-brightness: ;
            --tw-backdrop-contrast: ;
            --tw-backdrop-grayscale: ;
            --tw-backdrop-hue-rotate: ;
            --tw-backdrop-invert: ;
            --tw-backdrop-opacity: ;
            --tw-backdrop-saturate: ;
            --tw-backdrop-sepia:
        }

        ::backdrop {
            --tw-border-spacing-x: 0;
            --tw-border-spacing-y: 0;
            --tw-translate-x: 0;
            --tw-translate-y: 0;
            --tw-rotate: 0;
            --tw-skew-x: 0;
            --tw-skew-y: 0;
            --tw-scale-x: 1;
            --tw-scale-y: 1;
            --tw-pan-x: ;
            --tw-pan-y: ;
            --tw-pinch-zoom: ;
            --tw-scroll-snap-strictness: proximity;
            --tw-ordinal: ;
            --tw-slashed-zero: ;
            --tw-numeric-figure: ;
            --tw-numeric-spacing: ;
            --tw-numeric-fraction: ;
            --tw-ring-inset: ;
            --tw-ring-offset-width: 0px;
            --tw-ring-offset-color: #fff;
            --tw-ring-color: rgb(59 130 246 / 0.5);
            --tw-ring-offset-shadow: 0 0 #0000;
            --tw-ring-shadow: 0 0 #0000;
            --tw-shadow: 0 0 #0000;
            --tw-shadow-colored: 0 0 #0000;
            --tw-blur: ;
            --tw-brightness: ;
            --tw-contrast: ;
            --tw-grayscale: ;
            --tw-hue-rotate: ;
            --tw-invert: ;
            --tw-saturate: ;
            --tw-sepia: ;
            --tw-drop-shadow: ;
            --tw-backdrop-blur: ;
            --tw-backdrop-brightness: ;
            --tw-backdrop-contrast: ;
            --tw-backdrop-grayscale: ;
            --tw-backdrop-hue-rotate: ;
            --tw-backdrop-invert: ;
            --tw-backdrop-opacity: ;
            --tw-backdrop-saturate: ;
            --tw-backdrop-sepia:
        }

        .col-span-5 {
            grid-column: span 5 / span 5
        }

        .mx-auto {
            margin-left: auto;
            margin-right: auto
        }

        .my-\[0\.5rem\] {
            margin-top: 0.5rem;
            margin-bottom: 0.5rem
        }

        .mt-1 {
            margin-top: 0.25rem
        }

        .ml-\[1px\] {
            margin-left: 1px
        }

        .mt-\[2rem\] {
            margin-top: 2rem
        }

        .mt-\[0\.4rem\] {
            margin-top: 0.4rem
        }

        .flex {
            display: flex
        }

        .grid {
            display: grid
        }

        .h-\[3rem\] {
            height: 3rem
        }

        .w-full {
            width: 100%
        }

        .w-\[4rem\] {
            width: 4rem
        }

        .w-\[60\%\] {
            width: 60%
        }

        .table-auto {
            table-layout: auto
        }

        .border-collapse {
            border-collapse: collapse
        }

        .grid-cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr))
        }

        .flex-col {
            flex-direction: column
        }

        .items-center {
            align-items: center
        }

        .justify-end {
            justify-content: flex-end
        }

        .justify-center {
            justify-content: center
        }

        .justify-between {
            justify-content: space-between
        }

        .gap-1 {
            gap: 0.25rem
        }

        .gap-4 {
            gap: 1rem
        }

        .gap-3 {
            gap: 0.75rem
        }

        .whitespace-nowrap {
            white-space: nowrap
        }

        .rounded-sm {
            border-radius: 0.125rem
        }

        .border-\[2px\] {
            border-width: 2px
        }

        .border {
            border-width: 1px
        }

        .border-2 {
            border-width: 2px
        }

        .border-r-2 {
            border-right-width: 2px
        }

        .border-b-2 {
            border-bottom-width: 2px
        }

        .border-l-2 {
            border-left-width: 2px
        }

        .border-t-2 {
            border-top-width: 2px
        }

        .border-l-0 {
            border-left-width: 0px
        }

        .border-gray-400 {
            --tw-border-opacity: 1;
            border-color: rgb(156 163 175 / var(--tw-border-opacity))
        }

        .bg-white {
            --tw-bg-opacity: 1;
            background-color: rgb(255 255 255 / var(--tw-bg-opacity))
        }

        .bg-black {
            --tw-bg-opacity: 1;
            background-color: rgb(0 0 0 / var(--tw-bg-opacity))
        }

        .object-contain {
            object-fit: contain
        }

        .object-left {
            object-position: left
        }

        .p-2 {
            padding: 0.5rem
        }

        .p-1 {
            padding: 0.25rem
        }

        .py-2 {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem
        }

        .px-\[1rem\] {
            padding-left: 1rem;
            padding-right: 1rem
        }

        .py-\[1rem\] {
            padding-top: 1rem;
            padding-bottom: 1rem
        }

        .pb-\[1\.5rem\] {
            padding-bottom: 1.5rem
        }

        .pl-\[2px\] {
            padding-left: 2px
        }

        .text-left {
            text-align: left
        }

        .text-center {
            text-align: center
        }

        .text-sm {
            font-size: 0.875rem;
            line-height: 1.25rem
        }

        .text-xs {
            font-size: 0.75rem;
            line-height: 1rem
        }

        .font-semibold {
            font-weight: 600
        }

        .text-\[\#555555\] {
            --tw-text-opacity: 1;
            color: rgb(85 85 85 / var(--tw-text-opacity))
        }

        .text-black {
            --tw-text-opacity: 1;
            color: rgb(0 0 0 / var(--tw-text-opacity))
        }

        .text-white {
            --tw-text-opacity: 1;
            color: rgb(255 255 255 / var(--tw-text-opacity))
        }