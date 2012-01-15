<?php use \Site\Helper\Posts as Helper; ?>
</section>
			
			<aside>
				<ul id="categories">
					<?php
					
						foreach($menu as $item){
						
							echo '<li>'.$item.'</li>';
						
						}
					
					?>
				</ul>
			</aside>
				
			<?php 
			
				if(Helper::check_pub_dates() === true){
				
					echo '<aside>';
					Helper::pub_dates();
					echo '</aside>';
				
				}
			
			?>
			
			<div id="clear"></div>
       
        </section>
        	                    
        	       
        <footer>
         	Powered by <a href="http://www.lynxpress.org">Lynxpress</a> - <a href="index.php?ctl=contact">Contact</a>
        </footer>
        	        
        	            
	</body>
        	
</html>