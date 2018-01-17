@if (config('constants'))
    <script>
        (function(global) {
            let Valda = {};

            @foreach (config('constants') as $key => $value)
                @if (is_array($value) && count($value) > 0)
                    let {{ $key }} = {};

                    @foreach ($value as $k => $v)
                        {{ $key }}.{{ $k }} = '{{ $v }}';
                    @endforeach

                    Object.freeze({{ $key }});

                    Valda.{{ $key }} = {{ $key }};
                @elseif (is_string($value))
                    Valda.{{ $key }} = '{{ $value }}';
                @endif
            @endforeach

            Object.freeze(Valda);

            Object.defineProperty(global, 'Valda', {
                value: Valda,
                writable: false,
                enumerable: true,
                configurable: true,
            });
        })(window);
    </script>
@endif
