export function dateColor() {
    const dt = new Date();
    const dateField = document.querySelectorAll("tbody .datecontrol .dateinput");
    const today = `${dt.getFullYear()}${(dt.getMonth() + 1).toString().padStart(2, '0')}${dt.getDate().toString().padStart(2, '0')}`;
    dateField.forEach(item => {
        const answerDate = item.closest("tr").querySelector(".dateish .dateinput").value;
        const dateCtrl = item.value.split(".")[2] + item.value.split(".")[1] + item.value.split(".")[0];

        item.closest("td").classList.remove("expired");
        item.closest("td").classList.remove("during");

        if(+dateCtrl <= +today && answerDate === "") item.closest("td").classList.add("expired")
        if(+dateCtrl > +today && answerDate === "") item.closest("td").classList.add("during")
    })
}