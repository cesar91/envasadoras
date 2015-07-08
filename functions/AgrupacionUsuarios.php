<?php
	class AgrupacionUsuarios extends conexion {
		
		protected $rst_grupo_usuario;
				
        /*
         *  Carga un grupo de usuario basado en su nombre de de grupo
         */
		public function Load_Grupo_de_Usuario_By_Nombre($grupo_nombre){
			$query=sprintf("SELECT * FROM agrupacion_usuarios 
                              WHERE au_nombre='%s'",$grupo_nombre);
			$this->rst_grupo_usuario=parent::consultar($query);
			return parent::get_rows();
		}

        /*
         *  Carga un grupo de usuario basado en su ID
         */        
		public function Load_Grupo_de_Usuario_By_ID($id){
			$query=sprintf("SELECT * FROM agrupacion_usuarios 
                              WHERE au_id='%s'",$id);
            //error_log($query);
			$this->rst_grupo_usuario=parent::consultar($query);
			return parent::get_rows();
		}

        /*
         *  Carga una homologacion basado en el id de usuario
         */        
		public function Load_Homologacion_Dueno_By_u_ID($id){
			$query=sprintf("SELECT * FROM homologacion_dueno 
                              WHERE hd_u_id='%s'",$id);
            //error_log($query);
			$this->rst_grupo_usuario=parent::consultar($query);
			return parent::get_rows();
		}
		/*
         *  Carga el id de los usuarios que estan inscritos a ese grupo, sea controlling o finanzas
         */
		public function Load_Homologacion_Usuarios($id){
			$arr= array();
			$query=sprintf("SELECT hd_u_id FROM homologacion_dueno 
                              WHERE hd_au_id='%s'",$id);
            //error_log($query);
           return $this->rst_grupo_usuario=parent::consultar($query);	
		}

		/**
		 * Regresamos un dato del Usuario
		 */
		public function Get_dato($nombre){
			return(mysql_result($this->rst_grupo_usuario,0,$nombre));
		}
}
?>
