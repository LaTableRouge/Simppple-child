import '../styles/parts.scss' // mandatory for the Hot Module Reload

import.meta.glob(['../../blocks/**/scripts/*.js', '!**/build/*.js'], { query: '?blocks', eager: true })
import.meta.glob('../../patterns/**/scripts/*.js', { query: '?patterns', eager: true })
import.meta.glob('../../parts/**/scripts/*.js', { query: '?parts', eager: true })
import.meta.glob('../../templates/**/scripts/*.js', { query: '?templates', eager: true })

window.addEventListener('DOMContentLoaded', (e) => {})
