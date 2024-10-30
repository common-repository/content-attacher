function pagination(row_num){
		var req_num_row=row_num;

		var $tr=jQuery('#content_attacher tr:not(:first-child)');

		var total_num_row=$tr.length;
		var num_pages=0;
		if(total_num_row % req_num_row ==0){
			num_pages=total_num_row / req_num_row;
		}
		if(total_num_row % req_num_row >=1){
			num_pages=total_num_row / req_num_row;
			num_pages++;
			num_pages=Math.floor(num_pages++);
		}
		for(var i=1; i<=num_pages; i++){
			jQuery('#coat_pagination').append("<a href='#' class='btn'>"+i+"</a>");
		}
		$tr.each(function(i){
			jQuery(this).hide();
			if(i+1 <= req_num_row){
				$tr.eq(i).show();
			}

		});
		jQuery('#coat_pagination a').click(function(e){
        jQuery('#coat_pagination a').removeClass('active');
        jQuery(this).addClass('active');
			e.preventDefault();
			$tr.hide();
			var page=jQuery(this).text();
			var temp=page-1;
			var start=temp*req_num_row;
			//alert(start);

			for(var i=0; i< req_num_row; i++){

				$tr.eq(start+i).show();

			}
		});
  jQuery('#coat_pagination a:first').addClass('active');
}
function lookuptable(row_num) {
  // Declare variables
 var input, filter, table, tr, td, i,j;
  input = document.getElementById("coat_search");
  filter = input.value.toUpperCase();
  table = document.getElementById("content_attacher");
  tr = table.getElementsByTagName("tr");

  // Loop through all table rows, and hide those who don't match the search query
  if(filter.length!=0)
  {
  for (i = 1; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td") ;
    for(j=0 ; j<td.length ; j++)
    {
      let tdata = td[j] ;
      if (tdata) {
        if (tdata.innerHTML.toUpperCase().indexOf(filter) > -1) {
          tr[i].style.display = "";
          break ;
        } else {
          tr[i].style.display = "none";
        }
      }
     jQuery("#coat_pagination a").remove(".btn");
    }
  }
  }
  else
  {
  jQuery("#coat_pagination a").remove(".btn");
  pagination(row_num);
  }
}