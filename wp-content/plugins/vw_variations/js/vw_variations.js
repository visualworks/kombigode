jQuery(document).ready(function() {
  jQuery("#vw-create-variation").on("click", function() {
    var rows = jQuery("tbody.the-list tr.vw-product-list");
    var product_list = [];
    var i = 0;
    rows.each(function(index, element) {
      product_list.push(jQuery(element).find(".vw-product-id").html());
    });
    setInterval(function() {
      var data = {
        "action": "vw_create_variation",
        "vw_variation_post_id": product_list[i]
      };
      console.log(i, product_list[i], ajax_object.ajax_url);
      jQuery.ajax({
        url: ajax_object.ajax_url,
        data: data,
        cache: false,
        context: document.body,
        dataType: "json",
        type: "POST",
        success: function(data, textStatus, jqXHR) {
          console.log("Success: Product ID", data);
          jQuery("#row-" + data).remove();
        },
        complete: function(data) {
          console.log("Complete: Verify Product ID", product_list[i]);
        },
        error: function(jqXHR, textStatus, errorThrown) {
          jQuery("#row-" + product_list[i]).css({
            fontSize: "18px",
            color: "#f00"
          });
          console.error("Error: Product ID", product_list[i], textStatus, errorThrown);
        }
      });

      i++;
    }, 10000);
  });
});