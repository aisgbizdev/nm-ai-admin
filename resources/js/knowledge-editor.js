import Editor from "@toast-ui/editor";

document.addEventListener("DOMContentLoaded", () => {
    const containers = document.querySelectorAll("[data-tui-editor]");
    if (!containers.length) {
        return;
    }

    containers.forEach((container, idx) => {
        const targetName = container.getAttribute("data-editor-target") || "answer";
        const form = container.closest("form");
        const textarea =
            (form && form.querySelector(`textarea[name='${targetName}']`)) ||
            document.querySelector(`textarea[name='${targetName}']`);

        if (!textarea) {
            return;
        }

        const editor = new Editor({
            el: container,
            height: "420px",
            initialValue: textarea.value || "",
            initialEditType: "wysiwyg",
            previewStyle: "vertical",
            usageStatistics: false,
        });

        editor.on("change", () => {
            textarea.value = editor.getMarkdown();
        });

        if (form) {
            form.addEventListener("submit", () => {
                textarea.value = editor.getMarkdown();
            });
        }

        // simpan referensi & allow reset
        container.__tuiEditor = editor;
        container.dataset.tuiEditorId = `${idx}`;
        container.addEventListener("tui-editor:set", (event) => {
            const value = event.detail ?? "";
            editor.setMarkdown(value);
            textarea.value = value;
        });
    });
});
