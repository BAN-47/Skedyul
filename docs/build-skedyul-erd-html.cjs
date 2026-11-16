const fs = require('node:fs');
const path = require('node:path');

const directory = __dirname;
const diagram = fs.readFileSync(path.join(directory, 'skedyul-erd.mmd'), 'utf8').trim();
const title = 'Skedyul — Entity Relationship Diagram';
const html = `<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>${title}</title>
  <script src="https://cdn.jsdelivr.net/npm/mermaid/dist/mermaid.min.js"></script>
  <script>mermaid.initialize({ startOnLoad: true, theme: 'base', securityLevel: 'strict', er: { entityPadding: 6, diagramPadding: 6 }, themeVariables: { fontSize: '18px', primaryColor: '#ffffff', primaryTextColor: '#111827', primaryBorderColor: '#334155', lineColor: '#475569', secondaryColor: '#ffffff', tertiaryColor: '#ffffff', mainBkg: '#ffffff', background: '#ffffff', textColor: '#111827' } });</script>
  <style>
    * { box-sizing: border-box; }
    body { margin: 0; padding: 8px; background: #ffffff; color: #111827; font-family: Inter, "Segoe UI", Arial, sans-serif; }
    h1 { margin: 0 0 4px; font-size: clamp(24px, 2.5vw, 34px); }
    .intro { margin: 0 0 6px; color: #475569; font-size: 15px; line-height: 1.35; }
    .container { width: 100%; margin: auto; padding: 6px; overflow: auto; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; }
    .mermaid { min-width: 1950px; text-align: center; }
    .mermaid svg { max-width: none !important; }
    .mermaid .entityBox, .mermaid .attributeBoxOdd, .mermaid .attributeBoxEven { fill: #ffffff !important; stroke: #334155 !important; stroke-width: 1.5px !important; }
    .mermaid .entityLabel { font-size: 22px !important; font-weight: 700 !important; fill: #111827 !important; }
    .mermaid .attributeText { font-size: 16px !important; fill: #1f2937 !important; }
    .mermaid .relationshipLabel { font-size: 15px !important; fill: #111827 !important; }
    .mermaid .relationshipLine { stroke: #475569 !important; stroke-width: 1.8px !important; }
    .note { margin: 6px 0 0; color: #475569; font-size: 13px; line-height: 1.35; }
    @media print { body { padding: 0; background: white; } .container { border: 0; border-radius: 0; } .mermaid { min-width: 0; } }
  </style>
</head>
<body>
  <h1>Skedyul — Entity Relationship Diagram</h1>
  <p class="intro">Full ERD of unique tables represented by the current Skedyul Eloquent models. PK means primary key; FK means foreign key/reference. Relationship ends show one and many cardinalities.</p>
  <main class="container"><pre class="mermaid">
${diagram}
  </pre></main>
  <p class="note">Duplicate model aliases are consolidated (Settings/SystemSetting and classes in SupportingModels). Relationships are based on model relations and matching FK fields; verify physical constraints against the live Supabase schema before treating this as a database-generated schema diagram.</p>
</body>
</html>
`;
fs.writeFileSync(path.join(directory, 'skedyul-erd.html'), html, 'utf8');
