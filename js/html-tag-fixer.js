/* ---------- CodeMirror ---------- */
const editor = CodeMirror(document.getElementById("editor-wrapper"), {
  mode: "htmlmixed",
  lineNumbers: true,
  tabSize: 2,
  lineWrapping: true,
  viewportMargin: Infinity,
});

/* ---------- LocalStorage 快取 ---------- */
const LS_KEY = "htmlFixerCache";
if (localStorage.getItem(LS_KEY)) {
  editor.setValue(localStorage.getItem(LS_KEY));
}
const toastSaved = new bootstrap.Toast(document.getElementById("toastSaved"));
editor.on("change", () => {
  localStorage.setItem(LS_KEY, editor.getValue());
  toastSaved.show();
});

/* ---------- File Upload / Drag-drop ---------- */
document.getElementById("file").addEventListener("change", (e) => {
  const f = e.target.files[0];
  if (!f) return;
  const r = new FileReader();
  r.onload = (ev) => editor.setValue(ev.target.result);
  r.readAsText(f);
});
["dragenter", "dragover"].forEach((evt) => {
  editor.getWrapperElement().addEventListener(evt, (e) => {
    e.preventDefault();
    e.dataTransfer.dropEffect = "copy";
  });
});
editor.getWrapperElement().addEventListener("drop", (e) => {
  e.preventDefault();
  const f = e.dataTransfer.files[0];
  if (!f) return;
  const r = new FileReader();
  r.onload = (ev) => editor.setValue(ev.target.result);
  r.readAsText(f);
});

/* ---------- Helper：渲染錯誤 ---------- */
function renderErrors(errors) {
  const box = document.getElementById("errors");
  box.innerHTML = "";
  if (!errors.length) {
    box.innerHTML =
      '<p class="text-success d-flex align-items-center"><i class="fa-solid fa-circle-check me-1"></i>沒有發現錯誤！</p>';
    return;
  }
  const map = {
    unclosed: "未閉合",
    unmatched: "多餘關閉",
    "void-close": "Void tag 不該使用 </>",
  };
  errors.forEach((e) => {
    const div = document.createElement("div");
    div.className = "error";
    div.innerHTML = `<i class="fa-solid fa-triangle-exclamation me-1"></i>
          第 ${e.line + 1} 行：${map[e.type]} &lt;${e.tag}&gt;
          <button class="btn btn-sm btn-outline-secondary ms-2" onclick="fixLine(${
            e.line
          },'${encodeURIComponent(e.raw || "")}','${e.type}')">
            <i class="fa-solid fa-wrench"></i> 修復
          </button>`;
    // div.onclick = () => editor.setCursor({ line: e.line, ch: 0 });
    div.onclick = () => {
      const doc = editor.getDoc();
      const lineLength = doc.getLine(e.line).length;
      const from = { line: e.line, ch: 0 };
      const to = { line: e.line, ch: lineLength };

      editor.focus();
      doc.setCursor(from);

      // 自動捲動至中間
      const top = editor.charCoords(from, "local").top;
      const height = editor.getScrollerElement().clientHeight;
      editor.scrollTo(null, top - height / 2);

      // 高亮該行
      const marker = editor.markText(from, to, {
        className: "custom-selected-line",
      });
      setTimeout(() => marker.clear(), 2000);
    };
    box.appendChild(div);
  });
}

/* ---------- 清空編輯器 ---------- */
/* ---------- 下載 ---------- */
function downloadHTML() {
  const blob = new Blob([editor.getValue()], { type: "text/html" });
  const a = document.createElement("a");
  a.href = URL.createObjectURL(blob);
  a.download = "fixed.html";
  a.click();
}

/* ---------- 刪除「含 HTML 標籤」的註解 ---------- */
function removeCommentedHTMLTags() {
  const doc = editor.getDoc();
  const lines = doc.getValue().split("\n");
  const out = [];
  let inComment = false,
    buf = [];
  for (const line of lines) {
    if (!inComment && line.trim().startsWith("<!--")) {
      inComment = true;
      buf.push(line);
      if (line.includes("-->")) {
        end();
      }
    } else if (inComment) {
      buf.push(line);
      if (line.includes("-->")) {
        end();
      }
    } else {
      out.push(line);
    }
  }
  function end() {
    inComment = false;
    const joined = buf.join("\n");
    if (
      !(/^<!--[\s\S]*?-->$/.test(joined.trim()) && /<\s*\/?.*?>/.test(joined))
    ) {
      out.push(...buf);
    }
    buf = [];
  }
  doc.setValue(out.join("\n"));
}

/* ---------- 補結構（安全版） ---------- */

function finalizeDocumentStructureOld() {
  let html = editor.getValue().trim();

  /* --- 1) 暫存 script/style，避免被誤判 --- */
  const blocks = [];
  html = html.replace(
    /<\s*(script|style)(?=\s|>)[\s\S]*?<\/\s*\1\s*>/gi,
    (m) => {
      blocks.push(m);
      return `§§BLOCK${blocks.length - 1}§§`;
    }
  );

  /* --- 2) 砍掉重複的結尾標籤（防手抖） --- */
  html = html
    .replace(/(<\/html\s*>)(?=[\s\S]*<\/html\s*>)/gi, "")
    .replace(/(<\/body\s*>)(?=[\s\S]*<\/body\s*>)/gi, "");

  /* --- 3) 若完全沒有 <html>，整段包起來 --- */
  const hasHTML = /<\s*html[\s>]/i.test(html);
  if (!hasHTML) {
    html =
      '<!doctype html>\n<html lang="zh-Hant">\n<body>\n' +
      html +
      "\n</body>\n</html>";
  } else {
    /* 3-1) 有 <html> 但沒有 <body>：在 <html> 後插入 */
    const hasBody = /<\s*body[\s>]/i.test(html);
    if (!hasBody) {
      html = html.replace(/<\s*html[^>]*>/i, (m) => `${m}\n<body>`);
      html = html.replace(/<\/html\s*>/i, "</body>\n</html>");
    }

    /* 3-2) 有 <body> 但沒有 </body>：補一個在 </html> 前或文末 */
    if (/<\s*body[\s>]/i.test(html) && !/<\s*\/body\s*>/i.test(html)) {
      if (/<\s*\/html\s*>/i.test(html)) {
        html = html.replace(/<\/html\s*>/i, "</body>\n</html>");
      } else {
        html += "\n</body>";
      }
    }

    /* 3-3) 最後保險：沒有 </html> 就補尾巴 */
    if (!/<\s*\/html\s*>/i.test(html)) html += "\n</html>";
  }

  /* --- 4) 還原 script/style 區塊 --- */
  html = html.replace(/§§BLOCK(\d+)§§/g, (_, i) => blocks[i]);

  editor.setValue(html);
}

function finalizeDocumentStructure() {
  /*--------------------------------------------------
   * 0) 取得目前內容，去掉前後空白
   *-------------------------------------------------*/
  let html = editor.getValue().trim();

  /*--------------------------------------------------
   * 1) 暫存 <script>/<style>，避免被正規式干擾
   *-------------------------------------------------*/
  const blocks = [];
  html = html.replace(
    /<\s*(script|style)(?=\s|>)[\s\S]*?<\/\s*\1\s*>/gi,
    (m) => {
      blocks.push(m);
      return `§§BLOCK${blocks.length - 1}§§`;
    }
  );

  /*--------------------------------------------------
   * 2) 刪掉多餘的「結尾」</body>、</html>
   *    ── 只留第一個
   *-------------------------------------------------*/
  html = html
    .replace(/(<\/html\s*>)(?=[\s\S]*<\/html\s*>)/gi, "")
    .replace(/(<\/body\s*>)(?=[\s\S]*<\/body\s*>)/gi, "");

  /*--------------------------------------------------
   * 3) 處理「重複開啟」<body>：
   *    第一個保留；其餘統統降級成 <div>
   *-------------------------------------------------*/
  let firstBody = true;
  html = html.replace(/<\s*body\b([^>]*)>/gi, (m, attrs) => {
    if (firstBody) {
      firstBody = false;
      return m; // 保留第一個 <body>
    }
    return `<div${attrs}>`; // 之後的 <body> → <div>
    // 若想直接刪掉可改成：return '';
  });

  /*--------------------------------------------------
   * 4) 補齊結構標籤
   *-------------------------------------------------*/
  const hasHTML = /<\s*html[\s>]/i.test(html);
  const hasBody = /<\s*body[\s>]/i.test(html);

  // 4-1) 完全沒有 <html> → 整段包起來
  if (!hasHTML) {
    html =
      '<!doctype html>\n<html lang="zh-Hant">\n<body>\n' +
      html +
      "\n</body>\n</html>";
  } else {
    // 4-2) 有 <html> 但沒有 <body>
    if (!hasBody) {
      html = html.replace(/<\s*html[^>]*>/i, (m) => `${m}\n<body>`);
    }

    // 4-3) 有 <body> 但沒有 </body>
    if (/<\s*body[\s>]/i.test(html) && !/<\s*\/body\s*>/i.test(html)) {
      if (/<\s*\/html\s*>/i.test(html)) {
        html = html.replace(/<\/html\s*>/i, "</body>\n</html>");
      } else {
        html += "\n</body>";
      }
    }

    // 4-4) 沒 </html> 就補
    if (!/<\s*\/html\s*>/i.test(html)) html += "\n</html>";
  }

  /*--------------------------------------------------
   * 5) 還原 <script>/<style> 區塊
   *-------------------------------------------------*/
  html = html.replace(/§§BLOCK(\d+)§§/g, (_, i) => blocks[i]);

  /*--------------------------------------------------
   * 6) 寫回編輯器
   *-------------------------------------------------*/
  editor.setValue(html);
}

/* ---------- Analyzer ---------- */
// --- Analyzer: record script/style start line ---
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
  const tagRegex = /<!--[\s\S]*?-->|<\s*\/??\s*([a-zA-Z0-9]+)([^<>]*)?>/g;
  const errors = [];
  const stack = [];
  let match,
    isInSS = false,
    current = "",
    ssStart = -1;

  // Walk through tags
  while ((match = tagRegex.exec(html)) !== null) {
    const full = match[0];
    const tag = match[1] ? match[1].toLowerCase() : null;
    const isClosing = /^<\s*\//.test(full);
    const line = html.substring(0, match.index).split("\n").length - 1;
    if (full.startsWith("<!--")) continue;

    // Enter script/style
    if (!isInSS && /<\s*(script|style)(\s|>)/i.test(full)) {
      isInSS = true;
      current = tag;
      ssStart = line;
      continue;
    }
    // Inside script/style
    if (isInSS) {
      if (new RegExp(`<\\s*/\\s*${current}\\s*>`, `i`).test(full)) {
        isInSS = false;
        current = "";
      }
      continue;
    }

    // Void tags
    if (!tag) continue;
    if (voidTags.has(tag)) {
      if (isClosing) errors.push({ line, tag, type: "void-close", raw: full });
      continue;
    }

    // Normal tags
    if (!isClosing) {
      stack.push({ tag, line });
    } else {
      let found = false;
      for (let i = stack.length - 1; i >= 0; i--) {
        if (stack[i].tag === tag) {
          stack.splice(i, 1);
          found = true;
          break;
        }
      }
      if (!found) errors.push({ line, tag, type: "unmatched", raw: full });
    }
  }

  // Unclosed script/style
  if (isInSS && current && ssStart >= 0) {
    errors.push({ line: ssStart, tag: current, type: "unclosed", raw: "" });
  }
  // Other unclosed
  stack.forEach(({ tag, line }) =>
    errors.push({ line, tag, type: "unclosed" })
  );

  renderErrors(errors);
}

// --- fixLine: handle unclosed tags, including <script>/<style> ---
function fixLine(line, rawEncoded, type) {
  const raw = decodeURIComponent(rawEncoded);
  const doc = editor.getDoc();
  const total = doc.lineCount();
  const text = doc.getLine(line);
  let modified = false;

  /* ---------- 小工具 ---------- */
  const indentOf = (s) => (/^\s*/.exec(s) || [""])[0];
  const findLine = (regex, from = 0) => {
    for (let i = from; i < total; i++) if (regex.test(doc.getLine(i))) return i;
    return -1;
  };

  /* ---------- 0) 先自動補 </head>（若缺） ---------- */
  const headOpen = findLine(/<\s*head\b/i);
  if (headOpen > -1 && findLine(/<\s*\/head\b/i) === -1) {
    const bodyIdx = findLine(/<\s*body\b/i, headOpen + 1);
    if (bodyIdx > -1) {
      doc.replaceRange("\n</head>", { line: bodyIdx, ch: 0 });
      modified = true;
    }
  }

  /* ---------- 1) 若非 unclosed，就單純刪 raw ---------- */
  if (type !== "unclosed") {
    const replaced = text.replace(raw, "");
    doc.replaceRange(replaced, { line, ch: 0 }, { line, ch: text.length });
    modified = true;
  } else {
    /* ---------- 2) 分析 tag ---------- */
    const m = /<\s*([a-z0-9]+)/i.exec(text);
    const tag = m ? m[1].toLowerCase() : null;
    if (!tag) return;

    /* === 2-a) <script>/<style> 特殊處理 === */
    if (tag === "script" || tag === "style") {
      // 已經補過就跳過
      if (text.includes(`</${tag}>`)) return;

      // 之後行若已經有關閉標籤也跳過
      if (findLine(new RegExp(`<\\s*\\/\\s*${tag}\\b`, "i"), line + 1) > -1)
        return;

      /* -- style: 找最後一個 } → 在下一行補 </style> -- */
      if (tag === "style") {
        let insertAt = line + 1,
          lastBrace = -1;
        for (let i = line + 1; i < total; i++) {
          const l = doc.getLine(i);
          if (/^\s*<\/(head|body|html)\b/i.test(l) || /^\s*</.test(l)) {
            insertAt = lastBrace > -1 ? lastBrace + 1 : i;
            break;
          }
          if (l.trim().endsWith("}")) lastBrace = i;
        }
        doc.replaceRange(`\n${indentOf(text)}</style>`, {
          line: insertAt,
          ch: 0,
        });
        modified = true;
      } else {
        /* -- script: 找 </body> 之前 -- */
        const insertAt =
          findLine(/<\s*\/body\b/i, line + 1) > -1
            ? findLine(/<\s*\/body\b/i, line + 1)
            : total;
        doc.replaceRange(`\n${indentOf(text)}</script>`, {
          line: insertAt,
          ch: 0,
        });
        modified = true;
      }
      return; // 已處理完 script/style
    }

    /* === 2-b) 其他標籤 === */
    const INLINE = new Set([
      "p",
      "span",
      "a",
      "strong",
      "li",
      "dt",
      "dd",
      "td",
      "th",
      "option",
      "h1",
      "h2",
      "h3",
      "h4",
      "h5",
      "h6",
    ]);

    // inline：直接補在同一行尾
    if (INLINE.has(tag) && !text.includes(`</${tag}>`)) {
      doc.replaceRange(
        text + `</${tag}>`,
        { line, ch: 0 },
        { line, ch: text.length }
      );
      modified = true;
    } else {
      // block：補在 </body> 或 </html> 前
      let insertLine = findLine(/<\s*\/body\b/i, line + 1);
      if (insertLine === -1) insertLine = findLine(/<\s*\/html\b/i, line + 1);
      if (insertLine === -1) insertLine = total;

      // 若後方已經有閉合同名 tag，就不處理
      if (findLine(new RegExp(`<\\s*\\/\\s*${tag}\\b`, "i"), line + 1) === -1) {
        doc.replaceRange(`\n${indentOf(text)}</${tag}>`, {
          line: insertLine,
          ch: 0,
        });
        modified = true;
      }
    }
  }

  /* ---------- 3) 視覺高亮 ---------- */
  if (modified) {
    const end = doc.getLine(line).length;
    doc.setSelection({ line, ch: 0 }, { line, ch: end });
    const marker = doc.markText(
      { line, ch: 0 },
      { line, ch: end },
      { className: "fixed-line-highlight" }
    );
    setTimeout(() => marker.clear(), 2000);
  }
}

// ★★ 1) async 版 formatter
async function formatWithPrettier() {
  try {
    const formatted = await prettier.format(editor.getValue(), {
      parser: "html",
      plugins: window.prettierPlugins,
      tabWidth: 2,
      useTabs: false,
    });
    editor.setValue(formatted);
  } catch (err) {
    console.error("Prettier 格式化失敗：", err);
    alert("格式化失敗：" + err.message);
  }
}

/* ---------- 一鍵「修結構＋分析」 ---------- */
async function analyzeWithFix() {
  finalizeDocumentStructure();
  // await formatWithPrettier();      // 再跑 Prettier
  analyze();
}
document
  .getElementById("analyze-btn")
  .addEventListener("click", analyzeWithFix);
