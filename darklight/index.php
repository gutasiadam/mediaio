<html>
<div class="container">
<link rel="stylesheet" href="./darkmode.scss">
        <h1>Light / Dark Mode</h1>
        <div class="toggle-container">
            <input type="checkbox" id="switch" name="theme" /><label for="switch">Toggle</label>
        </div>

        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ducimus repellendus dolorem eum consequatur id exercitationem nesciunt, inventore modi perferendis impedit esse, tempora officia, ipsam quae libero. Nostrum, alias dignissimos.</p>
    </div>
</html>
<script>
        var checkbox = document.querySelector('input[name=theme]');

checkbox.addEventListener('change', function() {
    if(this.checked) {
        trans()
        document.documentElement.setAttribute('data-theme', 'dark')
    } else {
        trans()
        document.documentElement.setAttribute('data-theme', 'light')
    }
})

let trans = () => {
    document.documentElement.classList.add('transition');
    window.setTimeout(() => {
        document.documentElement.classList.remove('transition')
    }, 1000)
}
</script>