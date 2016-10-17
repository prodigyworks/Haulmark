
$(document).ready(
		function() {
			$(".close").click(
					function() { 
						$(this).parent().hide();
					}
				);
		}
	);

function attachDocumentView(name) {
	var modal = document.getElementById('documentPreviewModal');
	var modalImg = document.getElementById("iframe01");
	var captionText = document.getElementById("documentcaption");

	
	$(document).ready(function() {
		$("#" + name + "_cover").click(
				function() {
				    modal.style.display = "block";
				    modalImg.src = $("#" + name).attr("src");
				    modalImg.alt = $("#" + name).attr("alt");
				    captionText.innerHTML = this.alt;
					
				}
			);
	});
}
function attachImageView(name) {
	var modal = document.getElementById('myModal');
	
	// Get the image and insert it inside the modal - use its "alt" text as a caption
	var img = document.getElementById(name);
	var modalImg = document.getElementById("img01");
	var captionText = document.getElementById("caption");
	img.onclick = function(){
	    modal.style.display = "block";
	    modalImg.src = this.src;
	    modalImg.alt = this.alt;
	    captionText.innerHTML = this.alt;
	}

}