$(document).ready(function(){
  	var $modal = $('#modal');
    var image = document.getElementById('sample_image');
    var cropper;
    // Image1

    	$('#upload_image').change(function(event){
    		var files = event.target.files;
    		// console.log(files);

    		var done = function(url){
    			image.src = url;
    			// console.log(image.src);

    			const img = document.getElementById('sample_image');
    		  img.onload = function() {
    		    // alert(this.width + 'x' + this.height);
    		    if(this.width==1920 && this.height==800){
    		      // alert( 'you cannot crop the image'); // $(".crop").hide(); // $button.crop('hide');// $modal.modal('hide');// $('#crop').hide('fast');// $('#crop_p').hide('fast');
    		    var file = document.querySelector('input[type=file][id=upload_image]')['files'][0];
    		    var reader = new FileReader();
    		    reader.readAsDataURL(file);
    		    var baseString;
    		    reader.onloadend = function () {
    		        baseString = reader.result;
    						console.log("data to be sent");
    		        console.log(baseString);

    		        $.ajax({
    		          url:'index.php?r=slider/uploadfile',
    		          method:'POST',
    		          data:{image:baseString},
    		          success:function(data)
    		          {
    								console.log(data);
    		            // $modal.modal('hide');
    								var src = "/web/slider-images/"+data;
    		            $('#uploaded_image').attr('src', src);
    								$("#hd_img_1").val(data);
    		          }
    		        });

    		    };

    		    }else{
    		      // add the model code here so that if the image size is
    		      //not 1600 and 1200 then only the model popup is opened
    						$modal.modal('show');
    		      // also add base 64 image
    		    }
    		  }

    			// $modal.modal('show');
    		};

    		if(files && files.length > 0)
    		{
    			reader = new FileReader();
    			reader.onload = function(event)
    			{
    				done(reader.result);
    			};
    			reader.readAsDataURL(files[0]);
    		}
    	});

      // Image1 model and buttons for crop and upload

        $modal.on('shown.bs.modal', function() {
          cropper = new Cropper(image, {
            dragMode: 'none',
            aspectRatio: 4/3,
            autoCropArea: 0.65,
            restore: false,
            guides: false,
            center: false,
      			background:false,
            highlight: false,
            cropBoxMovable: true,
            cropBoxResizable: false,
            toggleDragModeOnDblclick: false,
      			// checkOrientation: true,
            preview:'.preview'
          });

        }).on('hidden.bs.modal', function(){
          cropper.destroy();
            cropper = null;
        });



        $('#crop').click(function(){
          canvas = cropper.getCroppedCanvas({
            width:1920,
            height:800,
            aspectRatio: "Free"
          });
          cropper.destroy();
            cropper = null;
          cropper = new Cropper(image, {
      			dragMode: 'none',
      			background:false,
            aspectRatio: 4/3,
      	    autoCropArea: 0.65,
            restore: false,
            guides: false,
            center: false,
            highlight: false,
            cropBoxMovable: true,
            cropBoxResizable: false,
            toggleDragModeOnDblclick: false,
      			// checkOrientation: true,
            // aspectRatio: 1,
            // viewMode: 3,
            preview:'.preview'
          });
        });

        $('#upload_img').click(function(){

          canvas = cropper.getCroppedCanvas({
            width:1920,
            height:800,
            aspectRatio: "Free"
          });

          canvas.toBlob(function(blob){
            url = URL.createObjectURL(blob);
      			var type = blob.type;
      			// url = URL.createObjectURL(blob);
      			url = canvas.toDataURL(type,0.5);
      			console.log("from ajax call");
      			console.log(url);
      			$.ajax({
      				url:'index.php?r=slider/uploadfile',
      				method:'POST',
      				data:{image:url},
      				success:function(data)
      				{
      					$modal.modal('hide');
      					$('#uploaded_image').attr('src', "/web/slider-images/"+data);
      					$("#hd_img_1").val(data);
      				}
      			});
          },'image/jpeg');

        });
});
