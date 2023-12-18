import '../scss/front.scss' // mandatory for the Hot Module Reload

import.meta.glob(['../../blocks/**/js/*.js', '!**/build/*.js'], { as: 'blocks', eager: true })
import.meta.glob('../../patterns/**/js/*.js', { as: 'patterns', eager: true })
import.meta.glob('../../parts/**/js/*.js', { as: 'parts', eager: true })
import.meta.glob('../../templates/**/js/*.js', { as: 'templates', eager: true })

window.addEventListener('DOMContentLoaded', (e) => {
	// Document ready
})
