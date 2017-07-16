// validate int for type_text_range
function type_text_range_check(input) {
    ch = input.value.replace(/[^\d]/g, '');
    if (ch.length == 1 && ch==0){ch = ch.slice(0, -1);}
    input.value = ch;
};