import '../styles/front.scss' // mandatory for the Hot Module Reload

import.meta.glob(['../../blocks/**/js/*.js', '!**/build/*.js'], { query: 'blocks', eager: true })
import.meta.glob('../../patterns/**/js/*.js', { query: 'patterns', eager: true })
import.meta.glob('../../parts/**/js/*.js', { query: 'parts', eager: true })
import.meta.glob('../../templates/**/js/*.js', { query: 'templates', eager: true })

window.addEventListener('DOMContentLoaded', (e) => {
	// Document ready
})
