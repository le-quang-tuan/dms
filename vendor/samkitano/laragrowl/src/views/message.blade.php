@if(Session::has('laragrowl_messages'))
    <script>
        @foreach(Session::get('laragrowl_messages') as $msg)
            $.jGrowl("{!! $msg['message'] !!}",
                {
                    theme:  "{!! $msg['type'] !!}",
                    header: "{!! $msg['header'] !!}",
                    sticky: "{!! $msg['sticky'] !!}" === "1",
                    group:  "{!! $msg['group'] !!}",
                    life:   parseInt("{!! $msg['life'] !!}")
                }
            );
        @endforeach
    </script>

    {{ Session::forget('laragrowl_messages') }}
@endif
