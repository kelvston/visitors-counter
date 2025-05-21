@extends('layouts.header')

@section('content')
    <style>
        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
            font-weight: 700;
            letter-spacing: 0.05em;
        }

        .input-section {
            max-width: 760px;
            margin: 0 auto 30px auto;
            background: #fff;
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 6px 12px rgb(0 0 0 / 0.1);
            display: flex;
            flex-wrap: wrap;
            gap: 15px 25px;
            align-items: center;
            justify-content: center;
        }

        .input-section label {
            font-weight: 600;
            color: #34495e;
            min-width: 90px;
        }

        .input-section input[type="text"],
        .input-section input[type="number"] {
            padding: 8px 12px;
            border: 1.8px solid #ced6e0;
            border-radius: 6px;
            font-size: 14px;
            width: 120px;
            transition: border-color 0.3s ease;
        }

        .input-section input[type="text"]:focus,
        .input-section input[type="number"]:focus {
            outline: none;
            border-color: #2980b9;
            box-shadow: 0 0 6px #74b9ffaa;
        }

        button {
            background-color: #2980b9;
            color: #fff;
            border: none;
            border-radius: 7px;
            padding: 10px 22px;
            font-size: 15px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-weight: 600;
            box-shadow: 0 4px 10px rgb(41 128 185 / 0.4);
        }

        button:hover {
            background-color: #1f6391;
            box-shadow: 0 6px 15px rgb(31 99 145 / 0.6);
        }

        #barcodes {
            display: none;
            max-width: 840px;
            margin: 0 auto;
            background: #fff;
            border-radius: 12px;
            padding: 12px 6mm 15px 6mm;
            box-shadow: 0 8px 24px rgb(0 0 0 / 0.12);
            /*display: grid;*/
            page-break-inside: avoid;
            max-height: 80vh;     /* Limit height */
            overflow-y: auto;     /* Scroll vertically if overflow */
            /*display: grid;*/
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 10px;
            grid-template-rows: repeat(13, 21.2mm);
            column-gap: 2mm;
            row-gap: 0.3mm;
            padding-top: 10mm; /* top page margin */
            padding-left: 6mm; /* left page margin */
            width: calc(5 * 38.1mm + 4 * 2mm + 6mm);
            box-sizing: border-box;
        }

        .barcode-container {
            box-sizing: border-box;
            width: 38.1mm;
            height: 21.2mm;
            padding-top: 7mm;   /* text top margin */
            padding-left: 2mm;  /* text left margin */
            border: 1.2px solid #dfe6e9;
            background-color: #fdfdfd;
            border-radius: 6px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            user-select: none;
            box-shadow: inset 0 0 4px rgb(0 0 0 / 0.05);
            transition: box-shadow 0.25s ease;
        }

        .barcode-container:hover {
            box-shadow: 0 0 8px #2980b9aa;
        }

        .barcode-title {
            font-size: 7pt;
            margin-bottom: 1mm;
            color: #2d3436;
            font-weight: 600;
        }

        .barcode-container canvas {
            flex-shrink: 0;
            width: 100%;
            height: auto;
            max-height: 10mm;
            filter: drop-shadow(0 0 1px rgba(0,0,0,0.15));
        }

        .barcode-code {
            font-size: 6pt;
            margin-top: 1mm;
            user-select: none;
            color: #636e72;
            font-family: 'Courier New', Courier, monospace;
            letter-spacing: 0.08em;
        }

        @media (max-width: 900px) {
            .input-section {
                flex-direction: column;
                gap: 12px;
            }

            .input-section label,
            .input-section input {
                width: 100%;
                min-width: auto;
            }

            #barcodes {
                width: 100%;
                grid-template-columns: repeat(auto-fit, minmax(38.1mm, 1fr));
            }
        }

    </style>

<h2>GENERATE BARCODE</h2>
<div class="row">
    <div class="col-md-6">
        <div class="input-section">
        <label>Title: </label>
        <input type="text" id="barcodeTitle" placeholder="Enter title for all barcodes" value="MY TITLE">
        <br><br>
        <label>Prefix: </label>
        <input type="text" id="prefix" value="THE " maxlength="10">
        &nbsp;&nbsp;
        <label>Start Number: </label>
        <input type="number" id="startNum" min="1" value="1">
        &nbsp;&nbsp;
        <label>End Number: </label>
        <input type="number" id="endNum" min="1" value="10">
        &nbsp;&nbsp;
        <label>Padded Length: </label>
        <input type="number" id="padLength" min="1" value="4">
        <br><br>
        <button onclick="generateBarcodes()">Generate</button>
        <button onclick="downloadPDF()">Export PDF</button>
    </div>
    </div>
    <div class="col-md-6">
         <div id="barcodes"></div>
    </div>
</div>
@endsection

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function padNumber(num, size) {
        let s = "0000000000" + num;
        return s.substr(s.length - size);
    }

    function generateBarcodes() {
        const container = document.getElementById("barcodes");
        container.innerHTML = "";
        container.style.display = "none";

        const title = document.getElementById("barcodeTitle").value.trim();
        const prefix = document.getElementById("prefix").value.trim().toUpperCase();
        const start = parseInt(document.getElementById("startNum").value);
        const end = parseInt(document.getElementById("endNum").value);
        const padLength = parseInt(document.getElementById("padLength").value);

        if (!title || !prefix || isNaN(start) || isNaN(end) || isNaN(padLength) ||
            start < 1 || end < start || (end - start > 5000) || padLength < 1) {
            alert("Please enter valid values (Max 5000 items).");
            return;
        }

        for (let i = start; i <= end; i++) {
            const code = prefix + padNumber(i, padLength);

            const div = document.createElement("div");
            div.className = "barcode-container";

            const titleLabel = document.createElement("div");
            titleLabel.className = "barcode-title";
            titleLabel.innerText = title;
            div.appendChild(titleLabel);

            const canvas = document.createElement("canvas");
            JsBarcode(canvas, code, {
                format: "CODE39",
                lineColor: "#000",
                width: 1.2,
                height: 20,
                displayValue: false
            });
            div.appendChild(canvas);

            const codeLabel = document.createElement("div");
            codeLabel.className = "barcode-code";
            codeLabel.innerText = prefix + " " + padNumber(i, padLength);
            div.appendChild(codeLabel);

            container.appendChild(div);
        }

        container.style.display = "grid";
        Swal.fire({
            title: 'Success!',
            text: 'Barcodes generated successfully.',
            icon: 'success',
            confirmButtonText: 'OK',
            timer: 2000,
            timerProgressBar: true
        });

    }


    async function downloadPDF() {
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF({
            unit: "mm",
            format: "a4",
            compress: true
        });

        const prefix = document.getElementById("prefix").value.trim().toUpperCase();
        const start = parseInt(document.getElementById("startNum").value);
        const end = parseInt(document.getElementById("endNum").value);
        const padLength = parseInt(document.getElementById("padLength").value);
        const title = document.getElementById("barcodeTitle").value.trim();

        // Layout constants
        const pageWidth = 210;
        const pageHeight = 297;

        const labelWidth = 38.1;
        const labelHeight = 21.2;

        const marginTop = 10;
        const marginLeft = 6;

        const gapCol = 2;
        const gapRow = 0.3;

        const cols = 5;
        const rows = 13;
        const labelsPerPage = cols * rows;

        // Text margins inside each label
        const textMarginTop = 7;
        const textMarginLeft = 2;

        // Helper to pad number
        function padNumber(num, size) {
            let s = "0000000000" + num;
            return s.substr(s.length - size);
        }

        // Create barcode canvas for code
        function createBarcodeCanvas(code) {
            const canvas = document.createElement("canvas");
            JsBarcode(canvas, code, {
                format: "CODE39",
                lineColor: "#000",
                width: 1.2,
                height: 20,
                displayValue: false,
                margin: 0
            });
            return canvas;
        }

        let currentIndex = 0;
        let totalLabels = end - start + 1;

        while (currentIndex < totalLabels) {
            if (currentIndex > 0) {
                pdf.addPage();
            }

            for (let row = 0; row < rows; row++) {
                for (let col = 0; col < cols; col++) {
                    if (currentIndex >= totalLabels) break;

                    const codeNumber = start + currentIndex;
                    const code = prefix + padNumber(codeNumber, padLength);

                    // Calculate top-left position of the label
                    const x = marginLeft + col * (labelWidth + gapCol);
                    const y = marginTop + row * (labelHeight + gapRow);

                    // Draw Title text inside label with text margins
                    pdf.setFontSize(7);
                    pdf.setTextColor(0, 0, 0);
                    pdf.text(title, x + textMarginLeft, y + textMarginTop);

                    // Draw barcode below the title inside the label
                    const canvas = createBarcodeCanvas(code);
                    const imgData = canvas.toDataURL("image/png");

                    // Position barcode roughly 2.5 mm below top text margin
                    const barcodeX = x + textMarginLeft;
                    const barcodeY = y + textMarginTop + 2.5;

                    // Calculate max barcode width inside label
                    const maxBarcodeWidth = labelWidth - 2 * textMarginLeft;
                    const barcodeHeight = 10; // fit nicely

                    // Draw barcode image
                    pdf.addImage(imgData, "PNG", barcodeX, barcodeY, maxBarcodeWidth, barcodeHeight);

                    // Draw the barcode number text below the barcode image
                    pdf.setFontSize(6);
                    // pdf.text(code, barcodeX, barcodeY + barcodeHeight + 3);  // 3 mm below barcode
                    pdf.text(prefix + " " + padNumber(codeNumber, padLength), barcodeX, barcodeY + barcodeHeight + 3);


                    currentIndex++;
                }
            }
        }

        pdf.save("barcodes.pdf");
        Swal.fire({
            title: 'Success!',
            text: 'Barcodes downloaded successfully.',
            icon: 'success',
            confirmButtonText: 'OK',
            timer: 2000,
            timerProgressBar: true
        });
    }
</script>

</body>
</html>
