let takeUrl = window.location.href;
const regex = /show_table/gm;
const str = takeUrl;
let m;
let count;
while ((m = regex.exec(str)) !== null) {

    if (m.index === regex.lastIndex) {
        regex.lastIndex++;
    }

    m.forEach((match, groupIndex) => {
        count = 1;
    });
}
if (count === 1){
    let hideElem = document.getElementsByClassName("card card-table")[0];
    hideElem.style.display = "block";
}else {

}

