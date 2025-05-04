@if(session('success'))
<script>
    Snackbar.show({
        text: "{{session('success')}}",
        pos: 'top-right',
        actionTextColor: '#fff',
        backgroundColor: '#8dbf42'
    });
</script>
@endif

@if(session('error'))
<script>
    Snackbar.show({
        text: "{{session('error')}}",
        pos: 'top-right',
        actionTextColor: '#fff',
        backgroundColor: '#e7515a'
    });
</script>
@endif
</body>
</html>