<script>
    export default {
        name: 'app_title',
        props: ['title'],
        watch: {
            title: {
                immediate: true,
                handler() {
                    document.title = this.title;
                }
            }
        },
        render () {
        },
    }
</script>