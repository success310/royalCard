import Alpine from 'alpinejs';
 
window.Alpine = Alpine;
 /*
 Alpine.store('darkMode', {
    init() {
        this.on = window.matchMedia('(prefers-color-scheme: dark)').matches
        this.setMode()
    },

    on: false,
 
    toggle() {
        this.on = ! this.on
        this.setMode()        
    },

    setMode() {
        if (this.on) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    }
})
 */
Alpine.start();