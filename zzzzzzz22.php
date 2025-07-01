<script>
	function analyze() {
		const html = editor.getValue();
		const voidTags = new Set([
			"area",
			"base",
			"br",
			"col",
			"embed",
			"hr",
			"img",
			"input",
			"link",
			"meta",
			"source",
			"track",
			"wbr",
		]);

		const tagRegex = /<!--[\s\S]*?-->|<\s*\/?\s*([a-zA-Z0-9]+)([^<>]*)?>/g;
		const errors = [];
		const stack = [];

		let match;
		let isInScriptOrStyle = false;
		let currentScriptOrStyle = "";

		while ((match = tagRegex.exec(html)) !== null) {
			const full = match[0];
			const tag = match[1] ? match[1].toLowerCase() : null;
			const isClosing = /^<\s*\//.test(full); // <- 改成 regex 判斷開頭
			const index = match.index;
			const line = html.substring(0, index).split("\n").length - 1;

			// 跳過註解 <!-- -->
			if (full.startsWith("<!--")) continue;

			// script/style 區塊處理
			if (!isInScriptOrStyle && /<\s*(script|style)(\s|>)/i.test(full)) {
				isInScriptOrStyle = true;
				currentScriptOrStyle = tag;
				continue;
			}

			if (isInScriptOrStyle) {
				const closeTagRegex = new RegExp(
					`<\\s*/\\s*${currentScriptOrStyle}\\s*>`,
					"i"
				);
				if (closeTagRegex.test(full)) {
					isInScriptOrStyle = false;
					currentScriptOrStyle = "";
				}
				continue;
			}

			if (!tag) continue;

			if (voidTags.has(tag)) {
				if (isClosing) {
					errors.push({
						line,
						tag,
						type: "void-close",
						raw: full
					});
				}
				continue;
			}

			if (!isClosing) {
				stack.push({
					tag,
					line
				});
			} else {
				let matched = false;
				for (let i = stack.length - 1; i >= 0; i--) {
					if (stack[i].tag === tag) {
						stack.splice(i, 1);
						matched = true;
						break;
					}
				}
				if (!matched) {
					errors.push({
						line,
						tag,
						type: "unmatched",
						raw: full
					});
				}
			}
		}

		stack.forEach(({
			tag,
			line
		}) => {
			errors.push({
				line,
				tag,
				type: "unclosed"
			});
		});

		renderErrors(errors);
	}
</script>