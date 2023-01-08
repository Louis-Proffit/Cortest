document.getElementById("file-picker").addEventListener("change", (event) => {
    const fileList = event.target.files;
    console.log(fileList);
})