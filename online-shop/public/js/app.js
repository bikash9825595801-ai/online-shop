document.addEventListener('DOMContentLoaded', function () {
    // Auto dismiss alerts
    document.querySelectorAll('.alert-dismissible').forEach(function (alert) {
        setTimeout(function () {
            if (window.bootstrap && window.bootstrap.Alert) {
                bootstrap.Alert.getOrCreateInstance(alert).close();
            }
        }, 6000);
    });

    // Confirmation dialogs for forms with data-confirm
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            var message = form.getAttribute('data-confirm') || 'Are you sure you want to continue?';
            if (!window.confirm(message)) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    });

    // Delete buttons (links) with data-confirm
    document.querySelectorAll('a[data-confirm]').forEach(function (link) {
        link.addEventListener('click', function (e) {
            var message = link.getAttribute('data-confirm') || 'Are you sure you want to continue?';
            if (!window.confirm(message)) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    });

    // Interactive rating stars (inputs with name ending in [rating])
    document.querySelectorAll('.rating-input').forEach(function (container) {
        var input = container.querySelector('input[type="hidden"]');
        var stars = container.querySelectorAll('.star');
        stars.forEach(function (star, index) {
            star.addEventListener('click', function () {
                if (input) {
                    input.value = index + 1;
                }
                stars.forEach(function (s, i) {
                    s.classList.toggle('selected', i <= index);
                });
            });
        });
    });

    // Price range display helper (live update of the value label)
    document.querySelectorAll('[data-price-output]').forEach(function (range) {
        var output = document.querySelector(range.getAttribute('data-price-output'));
        if (output) {
            range.addEventListener('input', function () {
                output.textContent = '$' + range.value;
            });
        }
    });

    // Cart quantity increment / decrement buttons
    document.querySelectorAll('.btn-inc, .btn-dec').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var form = btn.closest('form');
            var input = form.querySelector('input[name="quantity"]');
            var min = parseInt(input.min || 1, 10);
            var max = parseInt(input.max || 9999, 10);
            var current = parseInt(input.value, 10) || 1;
            var next = btn.classList.contains('btn-inc') ? current + 1 : current - 1;
            if (next >= min && next <= max) {
                input.value = next;
                form.submit();
            }
        });
    });
});
