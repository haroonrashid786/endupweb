<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .bg-gray {
            background-color: #ddd;
        }


        .bg-white {
            background-color: #fff;
        }

        .wrapper {
            /* max-width: 38rem; */
            min-width: 100%
            margin: auto;
            padding: 2rem 0;
        }

        /* .flex-gap-2 {
            display: flex;

            flex-direction: column;
            gap: 0.75rem;
        } */

        .content {
            padding: 0.5rem;
            border-radius: 4px;
        }

        .main {
            border-left: 3px dashed black;
            border-right: 3px dashed black;
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

        /*
        .flex-col {
            flex-direction: column
        } */

        .items-center {
            align-items: center
        }

        /* .justify-end {
            justify-content: flex-end
        } */

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

        .w-50 {
            width: 50%;
        }

        .float-left {
            float: left;
        }

        .float-right {
            float: right;
        }

        .clear-both {
            clear: both;
        }
    </style>
</head>

<body style="width: 100%">
    <div class="parent " style="width: 100%">
        <div class="wrapper flex-gap-2 "style="width: 100%">
            {{-- <h1 class="title">Example:</h1> --}}
            <div class="content bg-white">
                <div class="main">
                    <div style="width:80%;" class="float-left col-span-main">
                        <div class="bg-gray collect-amount">
                            <h3 class="" style="font-size: .3rem; line-height: 1">COD Collect Amount: Rs. 599.00</h3>
                        </div>
                        <div class="border-r-2 border-b-2 border-gray-400" style="height:7rem;">
                            <div class="w-50 float-left">
                                <div class="p-2 border-b-2 border-gray-400">
                                    <div class="flex items-center gap-1">
                                        <h1 class="font-semibold text-sm" style="font-size: .3rem; line-height: 1">Delivery Address:</h1>
                                        {{-- <p class="text-[#555555] text-sm" style="font-size: .3rem;line-height: 1">{{ $order['enduser_name'] }}</p> --}}
                                    </div>
                                    {{-- <p class="text-[#555555] text-sm" style="font-size: .3rem;line-height: 1">{{ $order['enduser_address'] }}</p> --}}
                                </div>
                            </div>
                            <div class="w-50 float-right border-l-2 border-gray-400" >
                                {{-- <img src="https://images.all-free-download.com/images/graphiclarge/qr_code_198897.jpg"
                                    class="w-full"> --}}
                                <img src="data:image/png;base64, {!! base64_encode(QrCode::format('svg')->size(100)->generate($qr)) !!} "
                                    class="">

                                    {{-- {!! QrCode::size(300)->generate($order['enduser_name']) !!} --}}
                                    {{-- {{  }} --}}
                            </div>
                            <div class="clear-both"></div>
                        </div>
                        <div class="bg-gray collect-amount" style="height: .5rem">
                            <div class=" items-center justify-between gap-4" style="width: 50%; float:left">
                                <h3 class=""style="font-size: .3rem;line-height: 1">
                                    <span class="font-semibold"style="font-size: .3rem;line-height: 1">Courier Name:</span> Rs. 599.00</h3>
                                <h3 class=""style="font-size: .3rem;line-height: 1">
                                    <span class="font-semibold"style="font-size: .3rem;line-height: 1">HBP:</span> Rs. 599.00</h3>
                            </div>
                            <div class="flex items-center justify-between gap-4" style="width: 50%; float:right; text-align: right">
                                <h3 class=""style="font-size: .3rem;line-height: 1">
                                    <span class="font-semibold"style="font-size: .3rem;line-height: 1">Courier Amount:</span> Rs. 599.00</h3>

                                <h3 class=""style="font-size: .3rem;line-height: 1">
                                    <span class="font-semibold"style="font-size: .3rem;line-height: 1">CPD:</span> Rs. 599.00
                                </h3>
                            </div>
                        </div>
                        <div class="border-gray-400 border-t-2 rounded-sm border-r-2 border-b-2 ">
                            <div class="p-2 pb-[1.5rem]">
                                <p class="text-[#555555] text-xs"style="font-size: .3rem;line-height: 1">
                                    <span class="font-semibold text-black">Sold By:</span>
                                    {{-- {{ $order['retailer']['user']['name'] }} {{ $order['retailer']['website'] }} --}}
                                </p>
                            </div>
                            <div class="p-2 text-sm border-gray-400 border-t-2">
                                <p class="text-[#555555] text-xs"style="font-size: .3rem;line-height: 1">
                                    <span class="font-semibold text-black">GSTIN No:</span>
                                    12312738217837281
                                </p>
                            </div>
                        </div>
                        <table
                            class="table-auto border-l-0 border-collapse border-gray-400 border-[2px] w-full mt-1 ml-[1px] text-sm" style="border: 1px solid">
                            <thead class="border border-l-0">
                                <tr class="">
                                    <th class="text-left border-gray-400 border-[2px] border-l-0 p-1" style="border: 1px solid" style="font-size: .3rem;line-height: 1;border: 1px solid">Product</th>
                                    <th class="text-center border-gray-400 border-[2px] border-l-0 p-1" style="border: 1px solid"style="font-size: .3rem;line-height: 1;border: 1px solid">QTY</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @foreach ($order['items'] as $item)


                                <tr>
                                    <td class="border-gray-400 border-[2px] border-l-0 text-left p-1" style="border: 1px solid"style="font-size: .3rem;line-height: 1;border: 1px solid">{{ $item['name'] }}</td>
                                    <td class="border-gray-400 border-[2px] border-l-0 text-center p-1" style="border: 1px solid"style="font-size: .3rem;line-height: 1;border: 1px solid">{{ $item['quantity'] }}</td>
                                </tr>
                                @endforeach
                                <tr> --}}
                                    <td class="border-gray-400 border-[2px] border-l-0 text-left p-1 font-semibold" style=""style="font-size: .3rem;line-height: 1;border: 1px solid">
                                        Total</td>
                                    <td class="border-gray-400 border-[2px] border-l-0 text-center p-1 font-semibold" style="border: 1px solid"style="font-size: .3rem;line-height: 1;border: 1px solid">
                                        123</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div style="width:20%;" class="float-right col-span-side flex flex-col gap-3 items-center" style="padding: 1rem; padding-left: 1rem">
                        <!-- <div class="h-[3rem] w-[3rem] flex items-center justify-center p-4 border-2 border-gray-400">
                            E
                        </div> -->
                        {{-- <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTbXMbdKcgE6UytGrNU8xbopE8tFABFdzqOLjYt7MPT7poBUTZS_JVf0bnWQfV3TIEdLg&amp;usqp=CAU"
                            class="w-[4rem] mt-[2rem] mx-auto"> --}}
                    </div>
                    <div class="clear-both"></div>
                    <div
                        class="col-span-5 border-2 border-gray-400 my-[0.5rem] py-2 flex flex-col gap-3 pl-[2px] border-l-0">
                        <div class="flex items-center gap-4 justify-between">
                            <div class="bg-black p-1 text-white text-sm"style="font-size: .3rem;line-height: 1">
                                Lorem, ipsum dolor.
                            </div>
                            <div>
                                <h1 class="font-semibold"style="font-size: .3rem;line-height: 1">STD</h1>
                            </div>
                        </div>
                        <p class="text-[#555555] text-xs"style="font-size: .3rem;line-height: 1">
                            <span class="font-semibold text-black">GSTIN No:</span>
                            12312738217837281
                        </p>
                        {{-- <img src="https://barcode-test.com/wp-content/uploads/bfi_thumb/image-38-327m7993mu3buxcxgb58zwoxyf14qrj9xskn8jzlkacpvkp8w.png"
                            class="w-[60%] h-[3rem]" style="width:20rem;"> --}}
                        <p class="text-[#555555] text-xs"style="font-size: .3rem;line-height: 1">
                            <span class="font-semibold text-black">GSTIN No:</span>
                            12312738217837281
                        </p>
                    </div>
                    <div class="flex justify-end col-span-5 px-[1rem] float-right">
                        <div class="py-[1rem]">
                            <p class="text-[#555555] text-sm"style="font-size: .3rem;line-height: 1">
                                Ordered Through
                            </p>
                            <div class="mt-[0.4rem] flex items-center gap-3">
                                <div>
                                    <h1 class="font-semibold whitespace-nowrap"style="font-size: .3rem;line-height: 1">ABC ABC</h1>
                                </div>
                                <div class="h-[3rem] w-[4rem] border border-gray-400 flex items-center justify-center" style="padding:0.6rem;text-align: center;"style="font-size: .3rem;line-height: 1">
                                    ABC
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clear-both"></div>
                </div>
            </div>
        </div>
    </div>


</body>

</html>
