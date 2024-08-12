
    @if(request('print_using_special_paper', 0) > 0)
        <script charset="utf-8">
            var print_using_special_paper = document.getElementsByClassName('print_using_special_paper')
            if (print_using_special_paper.length != 0) {
                window.ipcRenderer.send('print-data', {'html': print_using_special_paper[0].outerHTML});
            }
        </script>
    @endif
