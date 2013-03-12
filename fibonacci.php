<?php

# Tom Shehan
# 3/11/2013
 
# A demonstration of the performance advantage of dynamic programming, using memcached. 
# This compares the runtime of a naive recursive fibonacci function with that of a
# of a dynamic programming approach using memcached. 

# The naive recursive function tops out at about n=37 before computation becomes infeasible.
# The DP function can go as high as an iterative solution would.

# Note:	This is just to get familiar with memcached. It probably does not conform to best practices, 
#	and a DP fibonacci would be better off just using an array of stored values.


# memcached fibonacci - DP approach
function cache_fib($n,$cache){

	# base case
	if($n == 0 || $n == 1){
		return $n;
	}

	#recursive case with cached result
	$fib = $cache->get($n);
	if(!empty($fib)){
		return $fib ;
	}

	# recursive case with no cached result
	$fib = cache_fib($n - 1,$cache) + cache_fib($n - 2,$cache);
	$cache->set($n, $fib, 0);
	return $fib ;

}

# recursive fibonacci - standard approach 
function slow_fib($n){

	# base case
	if($n == 0 || $n == 1){
		return $n;
	}

	# recursive case
	return slow_fib($n - 1) + slow_fib($n - 2);

}

# perform the comparison
function compare_fibs($n){

	# connect to the memcached server
	$cache = new Memcached();
	$cache->addServer('localhost', 11211);

	# time the slow fib
	$slow_start = microtime();
	$fib = slow_fib($n);
	$slow_total = microtime() - $slow_start ;
	print("Recursive: fib($n) = $fib, time = $slow_total\n");

	# time the cache fib
	$cache_start = microtime();
	$fib = cache_fib($n,$cache);
	$cache_total = microtime() - $cache_start ;
	$cache->flush();
	print("Dynamic: fib($n) = $fib, time = $cache_total\n");

}

compare_fibs(35);

?>

