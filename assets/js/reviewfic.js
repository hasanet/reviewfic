"use strict";

document.getElementById('reviewfic-generate-shortcode').addEventListener('click', function() {
    var category = document.getElementById('reviewfic-category').value;
    var columns = document.getElementById('reviewfic-columns').value;
    var maxItems = document.getElementById('reviewfic-max-items').value;

    var shortcode = '[reviewfic category="' + category + '" columns="' + columns + '" max_items="' + (maxItems !== '' ? maxItems : 'Unlimited') + '"]';
    
    document.getElementById('reviewfic-shortcode-result').value = shortcode;
});
