<html>
    <body>
        <script>
            var hash = window.location.hash;

            if (! hash) {
                window.location.replace('/');
            }

            var url = window.location.href.substr(0, window.location.href.indexOf('#'));

            if (url && hash) {
                hash = encodeURIComponent(hash.substring(1));
                url += (url.indexOf('?') !== -1 ? '&' : '?') + 'hash=' + hash;

                window.location.replace(url);
            }
        </script>
    </body>
</html>
