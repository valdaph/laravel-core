@if (config('constants'))
    <script>
        (function(global) {
            let Constants = {};

            @foreach (config('constants') as $key => $value)
                @if (is_array($value) && count($value) > 0)
                    let {{ $key }} = {};

                    @foreach ($value as $k => $v)
                        {{ $key }}.{{ $k }} = '{{ $v }}';
                    @endforeach

                    Object.freeze({{ $key }});

                    Constants.{{ $key }} = {{ $key }};
                @elseif (is_string($value))
                    Constants.{{ $key }} = '{{ $value }}';
                @endif
            @endforeach

            Object.freeze(Constants);

            Object.defineProperty(global, 'Constants', {
                value: Constants,
                writable: false,
                enumerable: true,
                configurable: true,
            });
        })(window);
    </script>
@endif
