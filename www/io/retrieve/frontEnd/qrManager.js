//Scanner

const qrOnSuccess = (decodedText, decodedResult) => {
    console.log(`Code matched = ${decodedText}`, decodedResult);

    //Check if the scanned item is in the list
    let useritems = 0;
    let itemFound = false
    for (j = 0; j < useritems; j++) {
        if (decodedText == $('#retrieve_items').find('tr').eq(j).attr('id')) {
            //Check if the item is already in the list
            if ($('#retrieve_items').find('tr').eq(j).css('display') == 'none') {
                showToast(decodedText + " - már visszaadtad!", "red");
                console.log("Not available!");
                scan_fail_sfx.play();
                itemFound = true;
                return;
            } else {
                showToast(decodedText, "green");
                prepare(decodedText, decodedText, $('#retrieve_items').find('tr').eq(j).find('button').text().split('[')[0].trim());
                console.log("Prepared!");
                showToast(decodedText, "green");
                scan_succes_sfx.play();
                itemFound = true;
                return;
            }
        }
    }
    if (itemFound == false) {
        showToast("Ez az eszköz nincs nálad!", "red");
        scan_fail_sfx.play();
        console.log("Not available!");
    }
};