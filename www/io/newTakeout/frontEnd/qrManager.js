//Scanner

const qrOnSuccess = (decodedText, decodedResult) => {
    console.log(`Code matched = ${decodedText}`, decodedResult);

    let selectedItem = document.getElementById(decodedText);
    if (selectedItem) {
        selectedItem.click();
        showToast(decodedText, "green");
        scan_succes_sfx.play();
    } else {
        showToast("Nem található ilyen eszköz!", "red");
        scan_fail_sfx.play();
    }
};