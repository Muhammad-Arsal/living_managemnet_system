<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Document' }}</title>
    <style>
        @page { margin: 24mm 18mm; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11pt;
            color: #111;
            line-height: 1.45;
        }
        h1 { font-size: 16pt; margin: 0 0 6px; text-align: center; }
        h2 { font-size: 12pt; margin: 18px 0 8px; border-bottom: 1px solid #222; padding-bottom: 4px; }
        h3 { font-size: 11pt; margin: 14px 0 6px; }
        p { margin: 0 0 8px; }
        .meta { margin: 14px 0; }
        .meta-row { margin: 0 0 4px; }
        .label { font-weight: bold; }
        .value { border-bottom: 1px dotted #666; display: inline-block; min-width: 180px; padding: 0 4px; }
        .box {
            border: 1px solid #333;
            padding: 8px 10px;
            margin: 8px 0 12px;
            min-height: 28px;
            white-space: pre-wrap;
        }
        .section { margin-bottom: 12px; }
        .muted { color: #444; font-size: 10pt; }
        .signature { margin-top: 28px; }
        .signature-line { margin-top: 18px; }
        table.risk { width: 100%; border-collapse: collapse; margin: 8px 0 14px; }
        table.risk th, table.risk td {
            border: 1px solid #333;
            padding: 6px;
            vertical-align: top;
            font-size: 9.5pt;
        }
        table.risk th { background: #f3f3f3; text-align: left; }
        ul { margin: 4px 0 10px 18px; padding: 0; }
        li { margin-bottom: 3px; }
        .center { text-align: center; }
        .mt { margin-top: 16px; }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>
