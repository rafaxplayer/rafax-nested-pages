document.addEventListener('DOMContentLoaded', function () {
    const toggles = document.querySelectorAll('.widget_rafax_nested_pages .toggle-children');

    toggles.forEach(toggle => {
        toggle.addEventListener('click', function () {
            const childrenContainer = this.nextElementSibling.nextElementSibling; // Div contenedor de hijos
            if (childrenContainer.classList.contains('hidden')) {
                childrenContainer.classList.remove('hidden');
                this.innerHTML = '<svg id="Lager_1" style="enable-background:new -265 388.9 64 64;width:20px;height:20px" version="1.1" viewBox="-265 388.9 64 64" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g><path d="M-216.1,416.7l-15.1,14.8c-0.4,0.3-1.2,0.3-1.6,0l-15.1-14.8c-0.4-0.3,0.1-0.8,0.8-0.8h30.2   C-216.2,415.9-215.8,416.3-216.1,416.7z"/></g></svg>'; // Cambiar icono a desplegado
            } else {
                childrenContainer.classList.add('hidden');
                this.innerHTML = '<svg id="Lager_1" style="enable-background:new -265 388.9 64 64; width:20px;height:20px" version="1.1" viewBox="-265 388.9 64 64" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g><path d="M-239.1,407l14.8,15.1c0.3,0.4,0.3,1.2,0,1.6l-14.8,15.1c-0.3,0.4-0.8-0.1-0.8-0.8v-30.2   C-239.8,407.1-239.4,406.7-239.1,407z"/></g></svg></span>'; // Cambiar icono a colapsado
            }
        });
    });
});
