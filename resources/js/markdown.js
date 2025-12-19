import EasyMDE from "easymde";
import "easymde/dist/easymde.min.css";

function initEasyMDE() {
    const els = document.querySelectorAll('textarea[data-markdown="easymde"]');
    if (!els.length) return;

    els.forEach((el) => {
        if (el.dataset.inited === "1") return;
        el.dataset.inited = "1";

        const mde = new EasyMDE({
            element: el,
            spellChecker: false,
            status: false,
            autofocus: false,
            minHeight: "240px",
            autoDownloadFontAwesome: false, // biar ga ngandelin FA
            toolbar: [
                "bold",
                "italic",
                "strikethrough",
                "|",
                "heading-1",
                "heading-2",
                "heading-3",
                "|",
                "quote",
                "unordered-list",
                "ordered-list",
                "|",
                "code",
                "table",
                "link",
                "|",
                {
                    name: "hr",
                    title: "Horizontal Line",
                    className: "easymde-hr",
                    action: (editor) => {
                        const cm = editor.codemirror;
                        const doc = cm.getDoc();
                        const cursor = doc.getCursor();

                        // sisipkan HR markdown
                        doc.replaceRange("\n\n---\n\n", cursor);
                        cm.focus();
                    },
                },
            ],
        });

        // penting: sinkron ke textarea supaya yang kesimpan tetap markdown asli
        mde.codemirror.on("change", () => {
            el.value = mde.value();
        });
    });
}

document.addEventListener("DOMContentLoaded", initEasyMDE);
document.addEventListener("livewire:navigated", initEasyMDE);
// kalau ada turbo juga:
document.addEventListener("turbo:load", initEasyMDE);
