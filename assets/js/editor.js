import '../scss/editor.scss' // mandatory for the Hot Module Reload

const svgIcon = wp.element.createElement(
	'svg',
	{
		width: 80,
		height: 60,
		viewBox: '0 0 80 60',
		version: '1.1',
		xmlns: 'http://www.w3.org/2000/svg',
		style: {
			width: '24px',
			height: '24px',
			fill: '#000000'
		}
	},
	wp.element.createElement('path', {
		d: 'M32.589,9.773c0.371,0.238,1.015,0.504,1.921,0.723l3.53-1.658c-0.854-0.301-1.519-0.566-1.911-0.727  L32.589,9.773z M44.594,9.055c0.462,0.048,0.709-0.732-0.142-0.87c-5.541-0.808-7.199-1.84-7.892-2.353  c-0.606-0.403-0.709,1.049-0.241,1.299C38.76,8.365,41.065,8.819,44.594,9.055z M29.083,17.954c1.319-0.269,3.09-0.468,5.369-0.412  c-0.285-0.447-0.511-0.96-0.639-1.549C31.836,16.336,30.162,17.06,29.083,17.954z M52.432,43.41  c-0.864-0.004-1.695-0.004-2.438,0.002l2.106,1.017l0.219,3.965L52.432,43.41z M61.753,43.517l-1.118-0.02l0.039,0.648  L61.753,43.517z M39.558,51.517c0-2.435,0.581-4.262,1.47-5.625c0.46-0.703,0.965-0.56,1.374-0.189  c0.45-1.339,0.819-2.177,2.097-2.735c-6.535-1.504-11.587-6.467-11.683-11.898c-0.057-3.23,0.58-6.587,4.241-8.456l0.059-0.226  c-0.809-0.566-1.508-1.371-2.047-2.284c-2.337-0.101-4.11,0.078-5.384,0.324c-0.693,0.136-1.986,0.451-2.637,0.842  c-0.593,0.356-1.1,0.06-1.056-0.631c0.216-3.388,5.293-5.835,7.992-6.037c0.105-1.189,0.361-2.357,0.824-3.417  c-2.607-0.518-3.724-1.471-2.847-1.886l3.864-1.72c-0.462-0.402-0.574-1.131-0.377-1.728c0.064-0.202,0.162-0.376,0.282-0.507  c-0.626-1.905-0.176-4.329,3.063-3.361c5.499,1.641,4.16,4.31,9.446,4.297c4.501-0.009,4.182,3.874,1.704,4.528l0.121,0.23  c1.385,2.755,1.126,6.1,0.777,7.867c8.761,0.039,14.056,4.804,18.758,9.668c2.846,2.943,5.592,6.124,9.033,8.384  c1.442,0.947,2.973,1.698,4.661,2.182c0.602,0.172,2.227,0.501,2.314,1.192c0.188,1.482-1.89,1.243-2.598,1.233  c0,0-1.763-0.057-6.332-0.739c0.942,0.461,1.736,0.826,1.736,0.826c2.113,0.767,1.555,1.767,0.208,1.804  c-3.366,0.093-7.524,0.124-10.628,0.128c0.947,1.104,0.831,2.818,1.274,4.635l0.201,0.059c1.571,0.472,2.816,1.348,2.816,3.24v8.907  l-0.085,1.466l0.085,0.685v30.375c0,1.112-1.666,1.112-1.666,0V62.677l-0.092-0.732l0.092-1.57v-8.859  c0-1.169-0.628-1.298-1.33-1.505v44.597c0,1.11-1.667,1.11-1.667,0V48.778c0-0.091,0.013-0.173,0.031-0.25  c-0.023-0.077-0.05-0.158-0.073-0.244c-0.3-1.163-0.362-2.458-0.725-3.59c-0.16-0.495-1.916-0.67-2.608-0.707l-2.081,1.218  l-0.513,15.364v35.345c0,0.775-0.812,1.01-1.3,0.703c-0.313,0.738-1.598,0.617-1.598-0.357V50.691c0-0.449,0.267-0.715,0.59-0.804  l-0.357-5.983l-4.903,0.066l-0.586,25.785v28.062c0,1.112-1.667,1.112-1.667,0l-0.005-27.779l-1.338-24.536l-3.089-1.49  c-1.039,0.005-2.09,0.413-2.946,0.978c-0.237,0.363-0.478,1.437-0.813,2.29v49.326c0,1.114-1.667,1.114-1.667,0V47.95l-0.092-0.118  c-0.105,0.251-0.202,0.523-0.287,0.815v46.921c0,1.112-1.666,1.112-1.666,0v0.056c-0.214-0.131-0.37-0.365-0.37-0.704   M35.626,14.948l-0.006,0.103l-0.008,0.127v0.159c0,0.238,0.016,0.459,0.044,0.639c0.236,1.903,1.948,2.684,3.644,3.163  c0.919,0.259,0.658,1.489-0.289,1.35c-0.62-0.09-1.211-0.166-1.777-0.225c0.353,0.395,0.748,0.729,1.173,0.962l0.071,0.038  c0.729,0.389,0.295,1.485,0.127,2.035c-0.016,0.157-0.077,0.313-0.193,0.442c-0.11,0.138-0.256,0.225-0.412,0.265  c-2.962,1.42-3.569,3.965-3.516,7.034c0.093,5.362,6.422,10.856,14.362,10.715c1.19-0.019,2.736-0.02,4.376-0.014  c-5.235-1.059-9.321-4.842-9.853-9.731c-1.105-7.804,6.403-10.708,10.776-11.166c-1.352-0.224-2.801-0.318-4.366-0.254  c-0.768,0.032-1.037-0.758-0.762-1.259c0.315-0.931,0.977-4.716-0.44-7.534c-0.17-0.342-0.375-0.671-0.618-0.981  c-0.677-0.077-1.29-0.176-1.889-0.295c-3.834,0.984-7.092,1.138-9.537,0.909c-0.059,0.113-0.115,0.23-0.168,0.345  C35.947,12.708,35.711,13.794,35.626,14.948z M72.312,33.795c-0.272,3.145-2.97,6.62-6.684,8.202  c2.334,0.109,6.298,0.24,9.951,0.088c-0.703-0.33-4.064-1.611-3.377-2.974c0.196-0.39,0.543-0.51,0.865-0.455  c0.085-0.002,3.776,0.648,6.954,1.008c-0.801-0.387-1.565-0.828-2.3-1.311C75.741,37.052,73.984,35.466,72.312,33.795z   M41.375,13.896c-0.156-0.088-0.351-0.148-0.588-0.169c-1.105-0.109-0.944-1.769,0.16-1.662c2.224,0.217,2.795,2.08,2.57,3.365  c-0.062,0.778-0.708,1.398-1.506,1.415c-0.849,0.014-1.548-0.661-1.564-1.513C40.434,14.692,40.82,14.134,41.375,13.896z   M19.103,11.467c0.05-0.207-0.62-0.552-1.497-0.769c-0.876-0.218-1.631-0.23-1.682-0.023c-0.052,0.207,0.618,0.552,1.495,0.771  C18.297,11.667,19.05,11.674,19.103,11.467z M14.756,10.249c0.093-0.372,1.442-0.356,3.013,0.037  c1.572,0.392,2.771,1.011,2.674,1.395c-0.359,1.241-0.791,2.375-1.26,3.347c-0.259,0.548,0.41,0.713,0.41,0.713l7.004,1.52  c-0.311,0.307-0.598,0.623-0.859,0.953l-6.598-1.301c0,0-0.846-0.251-1.398,0.443c-0.732,0.839-1.412,1.229-2.003,1.081  C14.263,18.069,13.864,14.497,14.756,10.249z'
	})
)
wp.blocks.updateCategory('simppple-blocks', { icon: svgIcon })

// Gutenberg ready
if (document.querySelector('.block-editor__container')) {
	let blocksLoaded = false
	const blocksLoadedInterval = setInterval(function () {
		const editorWrapper = document.querySelector('.editor-styles-wrapper')
		if (editorWrapper) {
			blocksLoaded = true

			// DO code here
		}

		if (blocksLoaded) {
			clearInterval(blocksLoadedInterval)
		}
	}, 500)
}