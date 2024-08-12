
<style type="text/css">

  .leading-half { line-height: 0.5; }
  .leading-none { line-height: 1; }
  .leading-tight { line-height: 1.25; }
  .leading-medium { line-height: 1.35; }
  .leading-normal { line-height: 1.5; }
  .leading-loose {	line-height: 2;}


  .tracking-tighter { letter-spacing: -0.05em; }
  .tracking-tight { letter-spacing: -0.025em; }
  .tracking-normal { letter-spacing: 0em; }
  .tracking-wide { letter-spacing: 0.025em; }
  .tracking-wider { letter-spacing: 0.05em; }
  .tracking-widest { letter-spacing: 0.1em; }

  .flex { display: flex; }
  .inline { display: inline; }
  .inline-block { display: inline-block; }
  .block { display: block; }

  .items-start	{ align-items: flex-start; }
  .items-end	{ align-items: flex-end; }
  .items-center	{ align-items: center; }
  .items-baseline	{ align-items: baseline; }
  .items-stretch	{ align-items: stretch; }

  .justify-start {justify-content: flex-start; }
  .justify-end {justify-content: flex-end; }
  .justify-center {justify-content: center; }
  .justify-between {justify-content: space-between; }
  .justify-around {justify-content: space-around; }
  .justify-evenly {justify-content: space-evenly; }



  .tpa-0, .tpa-0 th, .tpa-0 td, table.has_border.tpa-0 td,table.has_border td.tpa-0 {padding: 0px; }
  .tpa-half, .tpa-half th, .tpa-half td, table.has_border.tpa-half td {padding: 0.5px; }
  .tpa-1, .tpa-1 th, .tpa-1 td, table.has_border.tpa-1 td {padding: 1px; }
  .tpa-2, .tpa-2 th, .tpa-2 td, table.has_border.tpa-2 td {padding: 2px; }
  .tpa-3, .tpa-3 th, .tpa-3 td, table.has_border.tpa-3 td {padding: 3px; }
  .tpa-4, .tpa-4 th, .tpa-4 td, table.has_border.tpa-4 td {padding: 4px; }
  .tpa-5, .tpa-5 th, .tpa-5 td, table.has_border.tpa-5 td {padding: 5px; }

  .tpy-1, .tpy-1 th, .tpy-1 td { padding-top: 1px; padding-bottom: 1px;}
  .tpy-2, .tpy-2 th, .tpy-2 td { padding-top: 2px; padding-bottom: 2px;}
  .tpy-3, .tpy-3 th, .tpy-3 td { padding-top: 3px; padding-bottom: 3px;}
  .tpy-4, .tpy-4 th, .tpy-4 td { padding-top: 4px; padding-bottom: 4px;}
  .tpy-5, .tpy-5 th, .tpy-5 td { padding-top: 5px; padding-bottom: 5px;}

  .tpx-1, .tpx-1 th, .tpx-1 td { padding-left: 1px; padding-right: 1px;}
  .tpx-2, .tpx-2 th, .tpx-2 td { padding-left: 2px; padding-right: 2px;}
  .tpx-3, .tpx-3 th, .tpx-3 td { padding-left: 3px; padding-right: 3px;}
  .tpx-4, .tpx-4 th, .tpx-4 td { padding-left: 4px; padding-right: 4px;}
  .tpx-5, .tpx-5 th, .tpx-5 td { padding-left: 5px; padding-right: 5px;}

  .ttop, .ttop th, .ttop td {vertical-align:top;}


  .has_border, .has_border th, .has_border td {
    border: 1px solid black;
  }

  .has_border.border-violet-100,
  .has_border.border-violet-100 th,
  .has_border.border-violet-100 td {
    border-color: #EDE9FE;
  }

  .has_border.border-violet-200,
  .has_border.border-violet-200 th,
  .has_border.border-violet-200 td {
    border-color: #DDD6FE;
  }

  .has_border.border-violet-500,
  .has_border.border-violet-500 th,
  .has_border.border-violet-500 td {
    border-color: #8b5cf6;
  }


  .has_border.border-blue-50,
  .has_border.border-blue-50 th,
  .has_border.border-blue-50 td {
    border-color: #EFF6FF;
  }

  .has_border.border-blue-100,
  .has_border.border-blue-100 th,
  .has_border.border-blue-100 td {
    border-color: #DBEAFE;
  }

  .has_border.border-blue-200,
  .has_border.border-blue-200 th,
  .has_border.border-blue-200 td {
    border-color: #BFDBFE;
  }


  .has_border.border-black,
  .has_border.border-black th,
  .has_border.border-black td {
    border-color: #000000;
  }


  .has_border .border.border-t-light,
  .has_border .border.border-t-light th,
  .has_border .border.border-t-light td {
    border-top-color: #F7FAFC;
  }

  .has_border .border.border-b-light,
  .has_border .border.border-b-light th,
  .has_border .border.border-b-light td {
    border-bottom-color: #F7FAFC;
  }

  .has_border .border.border-x-light,
  .has_border .border.border-x-light th,
  .has_border .border.border-x-light td {
    border-bottom-color: #F7FAFC;
    border-top-color: #F7FAFC;
  }

  .has_border.border-dark,
  .has_border.border-dark th,
  .has_border.border-dark td {
    border-color: #555555;
  }

  .has_border.border-width-2,
  .has_border.border-width-2 th,
  .has_border.border-width-2 td {
    border-width: 2px;
  }

  .has_border.inline_border {
    border-style: hidden;
    border-collapse: collapse;
  }

  .has_border.inline_horizontal_border tr:first-child td,
  .has_border.inline_horizontal_border tr:first-child th
	  { border-top-color: transparent; }
  .has_border.inline_horizontal_border tr td:first-child,
  .has_border.inline_horizontal_border tr th:first-child
	  { border-left-color: transparent; }
  .has_border.inline_horizontal_border tr:last-child td,
  .has_border.inline_horizontal_border tr:last-child th
	  { border-bottom-color: transparent; }
  .has_border.inline_horizontal_border tr td:last-child,
  .has_border.inline_horizontal_border tr th:last-child
	  { border-right-color: transparent;  }

  .border.border-top { border-top: 1px solid; }
  .border.border-bottom { border-bottom: 1px solid; }
  .border.border-right { border-right: 1px solid; }
  .border.border-gray { border-color: gray; }

  .border-none {border: unset;}
  .border-0 { border: 0px; }
  .border-1 { border: 1px solid; }
  .border-2 { border: 2px solid; }
  .border-3 { border: 3px solid; }

  .bg-gray-100{ background-color: #F7FAFC; }
  .bg-gray-200{ background-color: #EDF2F7; }
  .bg-gray-300{ background-color: #E2E8F0; }
  .bg-gray-400{ background-color: #CBD5E0; }
  .bg-gray-500{ background-color: #A0AEC0; }
  .bg-gray-600{ background-color: #718096; }
  .bg-gray-700{ background-color: #4A5568; }
  .bg-gray-800{ background-color: #2D3748; }

  .bg-violet-50  { background-color: #F5F3FF; }
  .bg-violet-100 { background-color: #EDE9FE; }
  .bg-violet-200 { background-color: #DDD6FE; }
  .bg-violet-300 { background-color: #DDD6FE; }
  .bg-violet-400 { background-color: #A78BFA; }
  .bg-violet-500 { background-color: #8B5CF6; }
  .bg-violet-600 { background-color: #7C3AED; }
  .bg-violet-700 { background-color: #6D28D9; }
  .bg-violet-800 { background-color: #5B21B6; }
  .bg-violet-900 { background-color: #4C1D95; }



  .bg-blue-50  { background-color: #EFF6FF; }
  .bg-blue-100 { background-color: #DBEAFE; }
  .bg-blue-200 { background-color: #BFDBFE; }
  .bg-blue-300 { background-color: #93C5FD; }
  .bg-blue-400 { background-color: #60A5FA; }
  .bg-blue-500 { background-color: #3B82F6; }
  .bg-blue-600 { background-color: #2563EB; }
  .bg-blue-700 { background-color: #1D4ED8; }
  .bg-blue-800 { background-color: #1E40AF; }
  .bg-blue-900 { background-color: #1E3A8A; }



  .bg-black{
    background-color: #000;
  }

  .red { color: red; }
  .white { color: white; }


  .gray-50 { color: #f9fafb; }
  .gray-100 { color: #f3f4f6; }
  .gray-200 { color: #e5e7eb; }
  .gray-300 { color: #d1d5db; }
  .gray-400 { color: #9ca3af; }
  .gray-500 { color: #6b7280; }
  .gray-600 { color: #4b5563; }
  .gray-700 { color: #374151; }
  .gray-800 { color: #1f2937; }
  .gray-900 { color: #111827; }


  .violet-50 { color: #F5F3FF; }
  .violet-100 { color: #EDE9FE; }
  .violet-200 { color: #DDD6FE; }
  .violet-300 { color: #DDD6FE; }
  .violet-400 { color: #A78BFA; }
  .violet-500 { color: #8B5CF6; }
  .violet-600 { color: #7C3AED; }
  .violet-700 { color: #6D28D9; }
  .violet-800 { color: #5B21B6; }
  .violet-900 { color: #4C1D95; }

  .purple-50 { color: #FAF5FF; }
  .purple-100 { color: #F3E8FF; }
  .purple-200 { color: #E9D5FF; }
  .purple-300 { color: #D8B4FE; }
  .purple-400 { color: #C084FC; }
  .purple-500 { color: #A855F7; }
  .purple-600 { color: #9333EA; }
  .purple-700 { color: #7E22CE; }
  .purple-800 { color: #6B21A8; }
  .purple-900 { color: #581C87; }


  .blue-50  { color: #EFF6FF; }
  .blue-100 { color: #DBEAFE; }
  .blue-200 { color: #BFDBFE; }
  .blue-300 { color: #93C5FD; }
  .blue-400 { color: #60A5FA; }
  .blue-500 { color: #3B82F6; }
  .blue-600 { color: #2563EB; }
  .blue-700 { color: #1D4ED8; }
  .blue-800 { color: #1E40AF; }
  .blue-900 { color: #1E3A8A; }




  .mh-4 { min-height: 40px; }

  .m-auto { margin: auto; }
  .ma-0{ margin: 0px; }
  .ma-half { margin: 5px; }
  .ma-1{ margin: 10px; }

  .mt-half{ margin-top: 5px; }
  .mt-1{ margin-top: 10px; }
  .mt-2{ margin-top: 20px; }
  .mt-3{ margin-top: 30px; }
  .mt-4{ margin-top: 40px; }

  .ml-0{ margin-left: 0px; }
  .ml-1{ margin-left: 10px; }
  .ml-2{ margin-left: 20px; }
  .ml-3{ margin-left: 30px; }
  .ml-4{ margin-left: 40px; }
  .ml-5{ margin-left: 50px; }
  .ml-6{ margin-left: 60px; }
  .ml-7{ margin-left: 70px; }
  .ml-8{ margin-left: 80px; }
  .mr-1{ margin-right: 10px; }
  .mr-2{ margin-right: 20px; }
  .mr-3{ margin-right: 30px; }

  .mb-0{ margin-bottom: 0px; }
  .mb-half{ margin-bottom: 5px; }
  .mb-1{ margin-bottom: 10px; }
  .mb-2{ margin-bottom: 20px; }
  .mb-3{ margin-bottom: 30px; }
  .mb-4{ margin-bottom: 40px; }
  .mb-5{ margin-bottom: 50px; }

  .mx-0{ margin: 0px 0px; }
  .mx-quarter{ margin: 0px 2px; }
  .mx-half{ margin: 0px 5px; }
  .mx-1{ margin: 0px 10px; }
  .mx-2{ margin: 0px 20px; }
  .mx-3{ margin: 0px 30px; }
  .mx-4{ margin: 0px 40px; }

  .pa-0{ padding: 0px; }
  .pa-quarter{ padding: 2px; }
  .pa-half{ padding: 5px; }
  .pa-1{ padding: 10px; }
  .pa-2{ padding: 20px; }

  .px-0{ padding: 0px 0px; }
  .px-quarter{ padding: 0px 2px; }
  .px-half{ padding: 0px 5px; }
  .px-1{ padding: 0px 10px; }
  .px-2{ padding: 0px 20px; }
  .px-3{ padding: 0px 30px; }
  .px-4{ padding: 0px 40px; }

  .py-0{ padding: 0px  0px;}
  .py-quarter{ padding: 2px 0px; }
  .py-half{ padding: 5px 0px; }
  .py-1{ padding: 10px 0px;}
  .py-2{ padding: 20px 0px;}
  .py-3{ padding: 30px 0px;}

  .pr-1{ padding-right: 10px; }
  .pr-2{ padding-right: 20px; }
  .pr-3{ padding-right: 30px; }
  .pr-4{ padding-right: 40px; }
  .pr-4{ padding-right: 50px; }

  .pt-0{ padding-top: 0px; }
  .pt-1px{ padding-top: 1px; }
  .pt-quarter{ padding-top: 2px; }
  .pt-half{ padding-top: 5px; }
  .pt-1{ padding-top: 10px; }
  .pt-2{ padding-top: 20px; }
  .pt-3{ padding-top: 30px; }
  .pt-4{ padding-top: 40px; }
  .pt-5{ padding-top: 50px; }

  .pb-0{ padding-bottom: 0px; }
  .pb-1{ padding-bottom: 10px; }
  .pb-2{ padding-bottom: 20px; }
  .pb-3{ padding-bottom: 30px; }

  .pl-0{ padding-left: 0px; }
  .pl-half{ padding-left: 5px; }
  .pl-1{ padding-left: 10px; }
  .pl-2{ padding-left: 20px; }
  .pl-3{ padding-left: 30px; }
  .pl-4{ padding-left: 40px; }

  .arial { font-family: arial;}

  .justify{ text-align: justify; }
  .left{ text-align: left; }
  .center{ text-align: center; }
  .right{ text-align: right; }

  .rtl{ direction: rtl; }
  .ltr{ direction: ltr; }

  .large{ font-size: 20px; }
  .bigger{ font-size: 16px; }
  .big{ font-size: 14px; }
  .medium{ font-size: 13px; }
  .small{ font-size: 10px; }
  .smaller{ font-size: 8px; }
  .tiny{ font-size: 6px; }
  .font-10{ font-size: 10px; }
  .font-11{ font-size: 11px; }
  .font-12{ font-size: 12px; }
  .font-30{ font-size: 30px; }
  .font-40{ font-size: 40px; }
  .font-50{ font-size: 50px; }

  .normal { font-weight: normal; }

  .italic{ font-style: italic; }

  .bold { font-weight: bold; }

  .underline { text-decoration: underline; }

  .upright{
    writing-mode: vertical-rl;
    text-orientation: mixed;
  }

  .upleft{
        writing-mode: tb-rl;
        transform: rotate(-180deg);
  }



  .align-top{ vertical-align: top; }

  .w-full { width: 100%; }
  .w-half { width: 50%; }
  .w-quarter { width: 25%; }
  .w-20p { width: 20%; }
  .w-25p { width: 25%; }
  .w-30p { width: 30%; }
  .w-35p { width: 35%; }
  .w-40p { width: 40%; }
  .w-49p { width: 49%; }
  .w-50p { width: 50%; }
  .w-55p { width: 55%; }
  .w-60p { width: 60%; }
  .w-65p { width: 65%; }
  .w-70p{ width: 70%; }
  .w-75p{ width: 75%; }
  .w-80p{ width: 80%; }
  .w-85p{ width: 85%; }
  .w-95p{ width: 95%; }
  .w-90p{ width: 90%; }

  .h-95p{ height: 95%; }

  .d-inline { display: inline; }
  .d-inline-block { display: inline-block; }

  .w-10 { width: 10px; }
  .w-20 { width: 20px; }
  .w-30 { width: 30px; }
  .w-40 { width: 40px; }
  .w-50 { width: 50px; }
  .w-60 { width: 60px; }
  .w-65 { width: 65px; }
  .w-70 { width: 70px; }
  .w-80 { width: 80px; }
  .w-90 { width: 90px; }
  .w-100 { width: 100px; }
  .w-110 { width: 110px; }
  .w-120 { width: 120px; }
  .w-130 { width: 130px; }
  .w-140 { width: 140px; }
  .w-150 { width: 150px; }
  .w-160 { width: 160px; }
  .w-170 { width: 170px; }
  .w-180 { width: 180px; }
  .w-190 { width: 190px; }
  .w-200 { width: 200px; }
  .w-250 { width: 250px; }
  .w-300 { width: 300px; }
  .w-400 { width: 400px; }
  .w-500 { width: 500px; }
  .w-600 { width: 600px; }
  .w-700 { width: 700px; }
  .w-800 { width: 800px; }
  .w-auto { width: auto; }

  .mw-10 { min-width: 10px; }
  .mw-20 { min-width: 20px; }
  .mw-30 { min-width: 30px; }
  .mw-35 { min-width: 35px; }
  .mw-40 { min-width: 40px; }
  .mw-50 { min-width: 50px; }
  .mw-60 { min-width: 60px; }
  .mw-70 { min-width: 70px; }
  .mw-75 { min-width: 75px; }
  .mw-80 { min-width: 80px; }
  .mw-90 { min-width: 90px; }
  .mw-100 { min-width: 100px; }
  .mw-110 { min-width: 110px; }
  .mw-120 { min-width: 120px; }
  .mw-130 { min-width: 130px; }
  .mw-140 { min-width: 140px; }
  .mw-150 { min-width: 150px; }
  .mw-160 { min-width: 160px; }
  .mw-200 { min-width: 200px; }
  .mw-250 { min-width: 250px; }

  .h-2 { height: 2px; }
  .h-3 { height: 3px; }
  .h-20 { height: 20px; }
  .h-100 { height: 100px; }
  .mh-100 { min-height: 100px; }

  .diagonal-top-to-bottom {
    position:relative;
  }

  .diagonal-top-to-bottom:after {
    content: "";
    position: absolute;
    border-top: 3px solid red;
    width: 40%;
    transform: rotate(-18deg);
    transform-origin: 0% 0%;
    left: 31%;
  }

</style>
