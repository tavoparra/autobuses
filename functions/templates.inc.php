<?php
class template{

        function template(){
		        $this->vars = array();		
		}

		
		function addTemplate($template_file)
		{
                $this->tpl_file = 'templates/' . $template_file . '.html';				
				$this->Finalstring=$this->agrega();
        }
			
        
        function asigna_variables( $vars )
		{
				require('config/varload.inc.php');
				$auxvars = array ( "LIGHTCOLOR" => $LIGHTCOLOR, "DARKCOLOR" => $DARKCOLOR );
				$this->vars = ( empty( $this->vars ) ) ? $vars : array_merge( $this->vars, $vars );
				$this->vars = array_merge( $this->vars, $auxvars );									
        }
		        
        function agrega()
		{
	        require( 'config/varload.inc.php' );
			if (!($this->fd = @fopen($this->tpl_file, 'r'))) 
			{
	          	die( 'error al abrir la plantilla ' . $this->tpl_file );
	        }
			else
			{
				if ( isset($ENCODED_VERSION) )
				{
	                $this->template_file = ioncube_read_file( $this->tpl_file );
				}
				else
				{
					$this->template_file = fread($this->fd, filesize($this->tpl_file));
					fclose( $this->fd );
	            }
	               
	            $this->mihtml = $this->template_file;
	            return $this->mihtml;
	              
	        }
	    }			
		
		function compile(){                
				$this->mihtml=$this->Finalstring;
				$this->mihtml = str_replace ("'", "\'", $this->mihtml);
				$this->mihtml = preg_replace('#\{([a-z0-9\-_]*?)\}#is', "' . $\\1 . '", $this->mihtml);
				if (isset($this->vars)){
                	reset ($this->vars);
                        while (list($key, $val) = each($this->vars)) {
                                $$key = $val;
                        }
                        eval("\$this->mihtml = '$this->mihtml';");
                        reset ($this->vars);
                        while (list($key, $val) = each($this->vars)) {
                                unset($$key);
                        }
					}
				$this->mihtml=str_replace ("\'", "'", $this->mihtml);				
        }
		
		function compileandgo(){                
				$this->compile();
				echo $this->mihtml;
        }
		
		function compileandsend(){                
				$this->compile();
				return $this->mihtml;
        }	
		
}
?>
