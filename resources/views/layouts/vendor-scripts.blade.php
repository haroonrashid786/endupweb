<!-- JAVASCRIPT -->
<script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/libs/metismenu/metismenu.min.js') }}"></script>
<script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/libs/node-waves/node-waves.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.datetimepicker.full.min.js') }}"></script>

<script src="{{ asset('assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.js') }}"></script>
<script>
    $('#change-password').on('submit', function(event) {
        event.preventDefault();
        var Id = $('#data_id').val();
        var current_password = $('#current-password').val();
        var password = $('#password').val();
        var password_confirm = $('#password-confirm').val();
        $('#current_passwordError').text('');
        $('#passwordError').text('');
        $('#password_confirmError').text('');
        $.ajax({
            url: "{{ url('update-password') }}" + "/" + Id,
            type: "POST",
            data: {
                "current_password": current_password,
                "password": password,
                "password_confirmation": password_confirm,
                "_token": "{{ csrf_token() }}",
            },
            success: function(response) {
                $('#current_passwordError').text('');
                $('#passwordError').text('');
                $('#password_confirmError').text('');
                if (response.isSuccess == false) {
                    $('#current_passwordError').text(response.Message);
                } else if (response.isSuccess == true) {
                    setTimeout(function() {
                        window.location.href = "{{ route('index') }}";
                    }, 1000);
                }
            },
            error: function(response) {
                $('#current_passwordError').text(response.responseJSON.errors.current_password);
                $('#passwordError').text(response.responseJSON.errors.password);
                $('#password_confirmError').text(response.responseJSON.errors
                    .password_confirmation);
            }
        });
    });
</script>
<script>
    @if (session()->has('success'))
        Snackbar.show({
            pos: 'bottom-center',
            text: '{{ session()->get('success') }}',
            backgroundColor: '#8bd2a4',
            actionTextColor: '#fff'
        });
    @endif

    @if (session()->has('error'))
        Snackbar.show({
            pos: 'bottom-center',
            text: '{{ session()->get('error') }}',
            backgroundColor: '#dc3545',
            actionTextColor: '#fff'
        });
    @endif

    @if (session()->has('validerrors'))
        @foreach (session()->get('validerrors') as $ve)
            Snackbar.show({
                pos: 'bottom-center',
                text: '{{ $ve }}',
                backgroundColor: '#dc3545',
                actionTextColor: '#fff'
            });
        @endforeach
    @endif

    $('.datetimepicker').datetimepicker();

    const datetimeInput = document.querySelector('.datetimepicker');
    const datetimeError = document.getElementById('datetime-error');

    datetimeInput.addEventListener('input', function(event) {
        const value = event.target.value;
        const isValid = /^\d{4}\/\d{2}\/\d{2} \d{2}:\d{2}$/.test(value);
        if (!isValid) {
            datetimeError.textContent = 'Invalid format (YYYY/MM/DD hh:mm)';
            datetimeInput.setCustomValidity('Invalid format (YYYY/MM/DD hh:mm)');
        } else {
            console.log('rereg');
            datetimeError.textContent = '';
            datetimeInput.setCustomValidity('');
        }
    });
</script>
{{-- <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>


<script>
    var pathsearch = "{{ url('/search/auto-compelete/') }}";
    var searchInput = document.querySelector('#seach-top');
    $(searchInput).typeahead({

        source: function(query, process) {
            if ($(searchInput).val().length > 3) {
                return $.get(pathsearch + '/' + query, function(data) {

                    if (data.length > 0) {
                        $('#search_list').empty();
                        $('#search_list').show();
                        data.forEach((item, index) => {
                            html =
                                `<li>
                                    <a href="/orders/items/${item.id}">
                                        ${item.order_number}<br>
                                        ${item.enduser_name}
                                        <hr>
                                        </a>
                                        </li>`;
                            $('#search_list').append(html);
                        })
                    } else {
                        $('#search_list').empty();
                        $('#search_list').hide();
                    }
                });
            } else {
                $('#search_list').empty();
                $('#search_list').hide();
            }
        }

    });
</script>
<script>
    var pathpostal = "{{ url('/search/postal/') }}";
    var postalInput = document.querySelector('#dropoff_postal');
    $(postalInput).typeahead({

        source: function(query, process) {
            if ($(postalInput).val().length > 2) {
                return $.get(pathpostal + '/' + query, function(data) {
                    console.log(data);
                    if (data.length > 0) {
                        $('#postal_list').empty();
                        $('#postal_list').show();
                        data.forEach((item, index) => {
                            html =
                                `<li><b>${item.name}</b> <br>`;

                            item.postalcodes.forEach((p) => {
                                html +=
                                    `<p onclick="selectPostal(this)" class="postalListItem">${p.postal} </p>`;
                            })
                            html += '</li>';

                            $('#postal_list').append(html);
                        })
                    } else {
                        $('#postal_list').empty();
                        $('#postal_list').hide();
                    }
                });
            } else {
                $('#postal_list').empty();
                $('#postal_list').hide();
            }
        }

    });

    function selectPostal(e) {
        // console.log();
        document.querySelector('#dropoff_postal').value = '';
        document.querySelector('#dropoff_postal').value = e.innerText;
        $('#postal_list').empty();
        $('#postal_list').hide();

    }
</script>
@yield('script')

<!-- App js -->
<script src="{{ asset('assets/js/app.min.js') }}"></script>

@yield('script-bottom')
