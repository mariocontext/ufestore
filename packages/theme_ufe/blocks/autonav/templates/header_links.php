<?php 
	defined('C5_EXECUTE') or die("Access Denied.");
	$aBlocks = $controller->generateNav();
	$c = Page::getCurrentPage();
	$containsPages = false;
	
	$nh = Loader::helper('navigation');
	$alllinks = array();
	
	//this will create an array of parent cIDs 
	$inspectC=$c;
	$selectedPathCIDs=array( $inspectC->getCollectionID() );
	$parentCIDnotZero=true;	
	while($parentCIDnotZero){
		$cParentID=$inspectC->cParentID;
		if(!intval($cParentID)){
			$parentCIDnotZero=false;
		}else{
			$selectedPathCIDs[]=$cParentID;
			$inspectC=Page::getById($cParentID);
		}
	} 	
	
	foreach($aBlocks as $ni) {
		$_c = $ni->getCollectionObject();
		if (!$_c->getCollectionAttributeValue('exclude_nav')) {
			
			
			$target = $ni->getTarget();
			if ($target != '') {
				$target = 'target="' . $target . '"';
			}
			if (!$containsPages) {
				// this is the first time we've entered the loop so we print out the UL tag
				//echo("<div class=\"nav-footer\">");
			}
			
			$containsPages = true;
			
			$thisLevel = $ni->getLevel();
			if($thisLevel > 0) {
				continue; /* This prevents sub level pages to be displayed are drop downs */
			}
			
			if ($thisLevel > $lastLevel) {
				//echo("<div>");
			} else if ($thisLevel < $lastLevel) {
				for ($j = $thisLevel; $j < $lastLevel; $j++) {
					if ($lastLevel - $j > 1) {
					//	echo("</div></div>");
					} else {
					//	echo("</div></div></div>");
					}
				}
			} else if ($i > 0) {
				// echo("</div>");
			}

			$pageLink = false;
			
			if ($_c->getCollectionAttributeValue('replace_link_with_first_in_nav')) {
				$subPage = $_c->getFirstChild();
				if ($subPage instanceof Page) {
					$pageLink = $nh->getLinkToCollection($subPage);
				}
			}
			
			if (!$pageLink) {
				$pageLink = $ni->getURL();
			}

			if ($c->getCollectionID() == $_c->getCollectionID()) { 
				$alllinks[] = ('<span class="nav-selected nav-path-selected"><a class="nav-selected nav-path-selected" ' . $target . ' href="' . $pageLink . '">' . $ni->getName() . '</a></span>');
			} elseif ( in_array($_c->getCollectionID(),$selectedPathCIDs) ) { 
				$alllinks[] = ('<span class="nav-path-selected"><a class="nav-path-selected" href="' . $pageLink . '" ' . $target . '>' . $ni->getName() . '</a><span>');
			} else {
				$alllinks[] = ('<span><a href="' . $pageLink . '" ' . $target . ' >' . $ni->getName() . '</a></span>');
			}	
			$lastLevel = $thisLevel;
			$i++;
		}
	}
	
	echo join(" ", $alllinks);
?>