function roundNumber(number,decimals) {
  var newString;// The new rounded number
  decimals = Number(decimals);
  if (decimals < 1) {
    newString = (Math.round(number)).toString();
  } else {
    var numString = number.toString();
    if (numString.lastIndexOf(".") == -1) {// If there is no decimal point
      numString += ".";// give it one at the end
    }
    var cutoff = numString.lastIndexOf(".") + decimals;// The point at which to truncate the number
    var d1 = Number(numString.substring(cutoff,cutoff+1));// The value of the last decimal place that we'll end up with
    var d2 = Number(numString.substring(cutoff+1,cutoff+2));// The next decimal, after the last one we want
    if (d2 >= 5) {// Do we need to round up at all? If not, the string will just be truncated
      if (d1 == 9 && cutoff > 0) {// If the last digit is 9, find a new cutoff point
        while (cutoff > 0 && (d1 == 9 || isNaN(d1))) {
          if (d1 != ".") {
            cutoff -= 1;
            d1 = Number(numString.substring(cutoff,cutoff+1));
          } else {
            cutoff -= 1;
          }
        }
      }
      d1 += 1;
    } 
    if (d1 == 10) {
      numString = numString.substring(0, numString.lastIndexOf("."));
      var roundedNum = Number(numString) + 1;
      newString = roundedNum.toString() + '.';
    } else {
      newString = numString.substring(0,cutoff) + d1.toString();
    }
  }
  if (newString.lastIndexOf(".") == -1) {// Do this again, to the new string
    newString += ".";
  }
  var decs = (newString.substring(newString.lastIndexOf(".")+1)).length;
  for(var i=0;i<decimals-decs;i++) newString += "0";
  //var newNumber = Number(newString);// make it a number if you like
  return newString; // Output the result to the form field (change for your purposes)
}

function update_total() {
  var totalRefacciones = 0;
  var totalMano = 0;
  var tasaImpuesto = jQuery("#tasa-impuesto").html().replace("%","");
  
  jQuery('.importeArticulo').each(function(i){
    importeArticulo = jQuery(this).html().replace("$","");
    if (!isNaN(importeArticulo)){
		totalRefacciones += Number(importeArticulo);
	}
  });
  
  jQuery('.importeMano').each(function(i){
    importeMano = jQuery(this).html().replace("$","");
    if (!isNaN(importeMano)){
		totalMano += Number(importeMano);
	}
  });

  totalRefacciones = roundNumber(totalRefacciones,2);
  totalMano = roundNumber(totalMano,2);
  var subtotal = (parseFloat(totalRefacciones) + parseFloat(totalMano)).toFixed(2);
  var impuesto = (tasaImpuesto * subtotal / 100).toFixed(2);
  var total = (parseFloat(subtotal) + parseFloat(impuesto)).toFixed(2);
  
  jQuery('#total-mano').html("$"+totalMano);
  jQuery('#total-refacciones').html("$"+totalRefacciones);
  jQuery('#subtotal').html("$"+subtotal);
  jQuery('#impuesto').html("$"+impuesto);
  jQuery('#total').html("$"+total);
}

function update_importe() {
  var row = jQuery(this).parents('.item-row');
  var importe = row.find('.cost').val().replace("$","") * row.find('.qty').val();
  //apply discount
  importe = importe - (importe * parseInt(jQuery(discount).val()) / 100);
  importe = roundNumber(importe,2);
  
  isNaN(importe) ? row.find('.importe').html("N/A") : row.find('.importe').html("$"+importe);
  
  update_total();
}

function bind() {
  jQuery(".cost").blur(update_importe);
  jQuery(".qty").blur(update_importe);
}

jQuery(document).ready(function() {

  jQuery('input').click(function(){
    jQuery(this).select();
  });
     
  bind();
  
  jQuery(".delete").live('click',function(){
    jQuery(this).parents('.item-row').remove();
    if (jQuery(".delete").length < 1){
		jQuery("#noRefaccion").show();
		jQuery("#refacciones_table").hide('slow');
	}
	else
		update_total();
  });
    
  jQuery("#agregar").click(function(){
		jQuery(this).attr('disabled', true);
		var $cantidad = jQuery("#cantidad_art");
		var $articulo = jQuery("#articulo_name");
		var $precio = jQuery("#precio");
		var $erroresDiv = jQuery("#erroresDiv");
		var error = false;
		var importeArticulo = 0;
		var precioAMostrar = 0;
		var claseImporte = '';
		
		$erroresDiv.html("");
		
		if($precio[0].value == "") {
			$erroresDiv.prepend("- Debe escribir el precio");
			$precio.focus();
			error = true;
			jQuery("#agregar").attr('disabled', false);
		};
		
		if($articulo[0].value == "") {
			if(error) $erroresDiv.prepend("<br/>");
			$erroresDiv.prepend("- Debe escribir el c&oacute;digo de art&iacute;culo");
			$articulo.focus();
			error = true;
			jQuery("#agregar").attr('disabled', false);
		};
		
		if($cantidad[0].value == "") {
			if(error) $erroresDiv.prepend("<br/>");
			$erroresDiv.prepend("- Debe escribir la cantidad");
			$cantidad.focus();
			error = true;
			jQuery("#agregar").attr('disabled', false);
		};

		if(error !== true){
			var parametros = {
				cantidad : $cantidad[0].value,
				clave: $articulo[0].value,
				precio: $precio[0].value,
				descuento: jQuery("#discount").val(),
				clienteid: jQuery("#clienteid").val(),
				tipo_cambio: jQuery("#tipo_cambio").val()
			};
			
			 jQuery.ajax({
				type: "GET",
				url: "cargararticulos.php",
				dataType:"json",
				data: parametros,
				success: function(articulo){
					if(articulo.error == undefined){
						if (jQuery(".delete").length == 0) jQuery("#refacciones_table").show('slow');
						importeArticulo = parametros.precio * parametros.cantidad;
						
						var newRow = '<tr class="item-row"><td><div class="delete-wpr">';
						newRow += '<textarea class="qty" onkeypress="return IsNumber(event);" name="qty[]" >' + parametros.cantidad + '</textarea>';
						newRow += '<a title="Eliminar refacci&oacute;n" href="javascript:;" class="delete" style="display: block;">X</a></div></td>';
						newRow += '<td>' + articulo.name + '</td><td>' + articulo.code + '<input type="hidden" name="articuloid[]" value="' + articulo.id + '" />';
						newRow += '<input type="hidden" class="mano" value="' + articulo.mano_obra + '" /></td>';
						//Si el precio es en dolares evitamos que sea editado y lo convertimos a pesos sólo para mostrarlo, se debe guardar en dolares
						if(articulo.dollars != true){
							newRow += '<td><textarea class="cost" onkeypress="return IsNumber(event)" name="price[]">$' + parametros.precio + '</textarea>';
						}
						else{
							precioAMostrar = parametros.precio * parametros.tipo_cambio;
							importeArticulo *= parametros.tipo_cambio;
							newRow += '<td><input type="hidden" name="price[]" value="' + parametros.precio + '" />' ;
							newRow += '<textarea class="cost" readonly >$' + precioAMostrar + '</textarea>';
						}
						newRow += '<input type="hidden" name="dollars[]" value="' + articulo.dollars + '" /></td>';
						//cambiamos la clase para la mano de obra para que se considere como tal en la suma
						claseImporte = (articulo.mano_obra == 1) ? 'importeMano' : 'importeArticulo';
						newRow += '<td class="clienteDesc">' + parametros.descuento + '%</td><td class="total-value"><div class="importe ' + claseImporte + '">$' + importeArticulo + '</div></td></tr>';
						jQuery("#trMano").before(newRow);
						bind();
						update_total();
						$cantidad.val("");
						$articulo.val("");
						$precio.val("");
						$cantidad.focus();
						
						jQuery("#noRefaccion").hide();
					}
					else{
						$erroresDiv.prepend(articulo.error);
						jQuery(articulo.error_field).focus().select();
					}
					jQuery("#agregar").attr('disabled', false);
				},
				error: function(){
					alert('Un error ha ocurrido, intente de nuevo');
					jQuery("#agregar").attr('disabled', false);
				}
			});
		}
	});
	
	jQuery(".campoProductos").keypress(function(e){
		if(e.which == 13){
			$addButton = jQuery("#agregar");
			if($addButton.attr("disabled") == false){
				$addButton.click();
			}
				e.preventDefault();
		}
	});
	
	jQuery("#refacciones_table").delegate( "textarea", "keypress", function(e){
		if(e.which == 13){
			jQuery(this).blur();
			e.preventDefault();
		}
	});
  
});