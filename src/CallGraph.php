<?php

include_once __DIR__ ."/../includes.php";
function GetFuncCall($current_node, $expr, $current_cfg, $classname, $classes, &$funcinfo){
    print "In is tainted expr ".get_class($expr)."\n";
    global $GLOBAL_COUNT;
    print ">>>>deepth ".$funcinfo->Deepth ."\n";
	if($expr == null)
		return [];
    //print "IN GetFuncCall " .get_class($expr) ."\n";
    if($expr instanceof PhpParser\Node\Stmt\Expression){
        return GetFuncCall($current_node, $expr->expr, $current_cfg, $classname, $classes, $funcinfo);
    }
    else if($expr instanceof PhpParser\Node\Expr\BinaryOp){
        $left = GetFuncCall($current_node, $expr->left, $current_cfg, $classname, $classes, $funcinfo);
        $right = GetFuncCall($current_node, $expr->right, $current_cfg, $classname, $classes, $funcinfo);
        $re = $left;
        foreach($right as $func)
            $re[] = $func;
        return $re;
    }
	else if($expr instanceof PhpParser\Node\Expr\BooleanNot){
		return GetFuncCall($current_node, $expr->expr,$current_cfg, $classname, $classes, $funcinfo);
	}
	else if ($expr instanceof PhpParser\Node\Expr\Assign || $expr instanceof PhpParser\Node\Expr\AssignOp || $expr instanceof PhpParser\Node\Expr\AssignRef){
        // Get right hand side value
        return GetFuncCall($current_node, $expr->expr, $current_cfg, $classname, $classes, $funcinfo);
        
	}
	else if ($expr instanceof PhpParser\Node\Expr\ArrayDimFetch) {
        print "here comes into arraydimfetch\n" ;

        return GetFuncCall($expr->dim, $current_node, $current_cfg, $classname, $classes, $funcinfo);
    }
	else if($expr instanceof PhpParser\Node\Expr\Variable){
        return [];
	}
	else if($expr instanceof PhpParser\Node\Expr\PostInc || 
			$expr instanceof PhpParser\Node\Expr\PostDec ||
			$expr instanceof PhpParser\Node\Expr\PreDec  ||
			$expr instanceof PhpParser\Node\Expr\PreInc ){
        return [];
    }
	else if($expr instanceof PhpParser\Node\Expr\UnaryMinus ||
			$expr instanceof PhpParser\Node\Expr\UnaryPlus){
		return GetFuncCall($current_node, $expr->expr,$current_cfg, $classname, $classes, $funcinfo);
	}
	else if($expr instanceof PhpParser\Node\Scalar\DNumber ||
			$expr instanceof PhpParser\Node\Scalar\LNumber ||
			$expr instanceof PhpParser\Node\Scalar\String_){
	//	print "number or string constant\n";
		return [];
	}
    else if($expr instanceof PhpParser\Node\Expr\FuncCall|| 
            $expr instanceof PhpParser\Node\Expr\MethodCall) {
            
        if($expr instanceof PhpParser\Node\Expr\FuncCall) {
            $classname = 'MAIN_CLASS';
            
        }
        else{
            if ($expr->var instanceof PhpParser\Node\Expr\PropertyFetch){
                print $funcinfo->callflow . "\n";
                $re = getValofPropertyFetch($expr->var, $current_node, $current_cfg, $classname, $classes, $funcinfo);
                $Obj = $re[0];
                $CallFuncClassName = $Obj->classname;
            }
            elseif($expr->var instanceof PhpParser\Node\Expr\Variable) {
                print $expr->var->name;
                $re = getValofVar($expr->var, $current_cfg, $funcinfo);
                $classname = $re[0];
            }
            else $classname  = $current_cfg->classname;
        }

        $re = [];
        $re[] = $classname .(string)$expr->name;
        foreach($expr->args as $arg) {
            $tre = GetFuncCall($current_node, $arg->value, $current_cfg, $classname, $classes, $funcinfo);
            foreach($tre as $func)
                $re[] = $func;
        }
        return $re;
    }

	else if ($expr instanceof PhpParser\Node\Expr\Print_){
		return GetFuncCall($current_node, $expr->expr, $current_cfg, $classname, $classes, $funcinfo);
	}
    else if($expr instanceof PhpParser\Node\Stmt\Return_){
        return GetFuncCall($current_node, $expr->expr, $current_cfg, $classname, $classes, $funcinfo);
    }
	//if exit() is tainted.
	elseif ($expr instanceof PhpParser\Node\Expr\Exit_){
		return GetFuncCall($current_node, $expr->expr, $current_cfg, $classname, $classes, $funcinfo);
	}
    else if ($expr instanceof PhpParser\Node\Identifier) {
    }
    else if ($expr instanceof PhpParser\Node\Stmt\Const_) {
    }
    else if($expr instanceof PhpParser\Node\Stmt\Echo_) {
        $re = [];
        foreach($expr->exprs as $echoexpr) {
            $tre= GetFuncCall($current_node, $echoexpr,$current_cfg, $classname, $classes, $funcinfo);
            foreach($tre as $func)
                $re[] = $func;
        }
        return $re;
    }
    else if( $expr instanceof PhpParser\Node\Expr\ConstFetch) {
    }
    else if( $expr  instanceof PhpParser\Node\Expr\New_){
        
    }
    else if($expr instanceof PhpParser\Node\Expr\PropertyFetch) {
        return [];
    }

    else if($expr instanceof PhpParser\Node\Expr\Property) {
        // Which should not happen ??
    }
    else if ($expr instanceof PhpParser\Node\Expr\Include_) {
        // Which should not happen ??
    }
    else if ($expr instanceof PhpParser\Node\Expr\Array_) {
        
    }
    else if ($expr instanceof PhpParser\Node\Expr\Ternary) {
        $re = [];
        $tre = GetFuncCall($current_node, $expr->cond, $current_cfg, $classname, $classes, $funcinfo);
        foreach($tre as $func)
            $re[] = $func;
        $tre = GetFuncCall($current_node, $expr->if, $current_cfg, $classname, $classes, $funcinfo);
        foreach($tre as $func)
            $re[] = $func;
        $tre = GetFuncCall($current_node, $expr->else, $current_cfg, $classname, $classes, $funcinfo);
        foreach($tre as $func)
            $re[] = $func;
        return $re;
    }
    else if($expr instanceof PhpParser\Node\Expr\BitwiseNot) {
        return GetFuncCall($current_node, $expr->expr, $current_cfg, $classname, $classes, $funcinfo);
    }
    else if($expr instanceof PhpParser\Node\Expr\Cast) {
        return GetFuncCall($current_node, $expr->expr, $current_cfg, $classname, $classes, $funcinfo);
    }
    else if ($expr instanceof PhpParser\Node\Expr\ClassConstFetch) {
    }

    else if( $expr instanceof PhpParser\Node\Expr\Clone_) {
        print "This is TODO Node\Expr\Clone_\n";
        return  GetFuncCall($current_node, $expr->expr, $current_cfg, $classname, $classes, $funcinfo);
    }
    else if ($expr instanceof PhpParser\Node\Expr\Empty_) {
        // Empty_ means  the expr is empty or not
        // return true or false
        return GetFuncCall($current_node, $expr->expr, $current_cfg, $classname, $classes, $funcinfo);

    }
    else if ($expr instanceof PhpParser\Node\Expr\Error) {
        print "You are in Error Node\n";
        print "PhpParser goes wrong when parsing\n";
    }
    else if ($expr instanceof PhpParser\Node\Expr\ErrorSuppress) {
        print "You are in ErrorSuppress node\n";
        print "I don't understand this node\n";
        return GetFuncCall($current_node, $expr->expr, $current_cfg, $classname, $classes, $funcinfo);

    }
    else if ($expr instanceof PhpParser\Node\Expr\Eval_) {
        print "You are in Eval_ node\n";
        print "I don't understand this node\n";
        print "It is said to be dangerous for this node\n";
        return GetFuncCall($current_node, $expr->expr, $current_cfg, $classname, $classes, $funcinfo);
    }
    else if ($expr instanceof PhpParser\Node\Expr\Instanceof_) {
        print "This is TODO Node\Expr\Instanceof_\n";
        return GetFuncCall($current_node, $expr->expr, $current_cfg, $classname, $classes, $funcinfo);
    }
    else if ($expr instanceof PhpParser\Node\Expr\Isset_) {
        $re = [];
        print "This is TODO Node\Expr\Isset_\n";
        foreach($expr->vars as $var) {
            $tre = GetFuncCall($current_node, $var, $current_cfg, $classname, $classes, $funcinfo);
            foreach($tre as $func)
                $re[] = $func;
        }
        return $re;
    }
    else if ($expr instanceof PhpParser\Node\Expr\ShellExec) {
        print "This is TODO Node\Expr\ShellExec\n";
    }
    else if ($expr instanceof PhpParser\Node\Expr\Yield_) {
        print "This is TODO Node\Expr\Yield_\n";
    }
    else if ($expr instanceof PhpParser\Node\Expr\YieldFrom) {
        print "This is TODO Node\Expr\YieldFrom\n";
    }
    else {
        //print get_class($expr) ."\n";
    }
        return [];
}

