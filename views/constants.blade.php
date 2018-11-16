@if (config('constants'))
    <script>
        (function(global) {
            let Constants = {};

            @foreach (config('constants') as $key => $value)
                @if (is_array($value) && count($value) > 0)
                    let {{ $key }} = {};

                    @foreach ($value as $k => $v)
                        @if (is_int($v) || is_float($v))
                            {{ $key }}['{{ $k }}'] = {{ $v }};
                        @elseif (is_bool($v))
                            {{ $key }}['{{ $k }}'] = {{ $v ? 'true' : 'false' }};
                        @else
                            {{ $key }}['{{ $k }}'] = '{{ $v }}';
                        @endif
                    @endforeach

                    Object.freeze({{ $key }});

                    Constants['{{ $key }}'] = {{ $key }};
                @elseif (is_int($value) || is_float($value))
                    Constants['{{ $key }}'] = {{ $value }};
                @elseif (is_bool($value))
                    Constants['{{ $key }}'] = {{ $value ? 'true' : 'false' }};
                @else
                    Constants['{{ $key }}'] = '{{ $value }}';
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

@if (isset($constants) && config($constants))
    <script>
        (function(global) {
            let {{ studly_case($constants) }} = {};

            @foreach (config($constants) as $key => $value)
                @if (is_array($value) && count($value) > 0)
                    let {{ $key }} = {};

                    @foreach ($value as $k => $v)
                        @if (is_int($v) || is_float($v))
                            {{ $key }}['{{ $k }}'] = {{ $v }};
                        @elseif (is_bool($v))
                            {{ $key }}['{{ $k }}'] = {{ $v ? 'true' : 'false' }};
                        @else
                            {{ $key }}['{{ $k }}'] = '{{ $v }}';
                        @endif
                    @endforeach

                    Object.freeze({{ $key }});

                    {{ studly_case($constants) }}['{{ $key }}'] = {{ $key }};
                @elseif (is_int($value) || is_float($value))
                    {{ studly_case($constants) }}['{{ $key }}'] = {{ $value }};
                @elseif (is_bool($value))
                    {{ studly_case($constants) }}['{{ $key }}'] = {{ $value ? 'true' : 'false' }};
                @else
                    {{ studly_case($constants) }}['{{ $key }}'] = '{{ $value }}';
                @endif
            @endforeach

            Object.freeze({{ studly_case($constants) }});

            Object.defineProperty(global, '{{ studly_case($constants) }}', {
                value: {{ studly_case($constants) }},
                writable: false,
                enumerable: true,
                configurable: true,
            });
        })(window);
    </script>
@endif
