<?php
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('app/Http/Controllers'));

foreach ($files as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') continue;
    
    $code = file_get_contents($file->getRealPath());
    $tokens = token_get_all($code);
    
    // We will do a basic analysis of functions
    // Find all functions and check if any variable used inside is not a param, not declared, not $this, not superglobal
    // Especially closure `use (...)`
    
    $superglobals = ['$GLOBALS', '$_SERVER', '$_GET', '$_POST', '$_FILES', '$_COOKIE', '$_SESSION', '$_REQUEST', '$_ENV', '$this'];
    
    for ($i = 0; $i < count($tokens); $i++) {
        if ($tokens[$i][0] === T_FUNCTION) {
            $fnLine = $tokens[$i][2];
            // Get params
            $j = $i + 1;
            while ($j < count($tokens) && $tokens[$j] !== '(') $j++;
            $j++; // inside params
            $params = [];
            $paramDepth = 1;
            while ($j < count($tokens) && $paramDepth > 0) {
                if ($tokens[$j] === '(') $paramDepth++;
                elseif ($tokens[$j] === ')') $paramDepth--;
                elseif (is_array($tokens[$j]) && $tokens[$j][0] === T_VARIABLE) {
                    $params[] = $tokens[$j][1];
                }
                $j++;
            }
            
            // Check if there is `use (...)`
            $uses = [];
            $k = $j;
            while ($k < count($tokens) && $tokens[$k] !== '{' && $tokens[$k] !== ';') {
                if (is_array($tokens[$k]) && $tokens[$k][0] === T_USE) {
                    $k++;
                    while ($k < count($tokens) && $tokens[$k] !== '(') $k++;
                    $k++;
                    while ($k < count($tokens) && $tokens[$k] !== ')') {
                        if (is_array($tokens[$k]) && $tokens[$k][0] === T_VARIABLE) {
                            $uses[] = [$tokens[$k][1], $tokens[$k][2]];
                        }
                        $k++;
                    }
                    break;
                }
                $k++;
            }
            
            // If it's a closure `use (...)`, are the used variables defined in parent scope?
            // (We'll check closure `use (...)` specifically)
            if (!empty($uses)) {
                // Find parent function's available vars up to this point
                // For now, let's just log uses that might be suspicious
                foreach ($uses as $u) {
                    // Check if variable is defined before closure in the same file lines
                    $varName = $u[0];
                    $useLine = $u[1];
                    // Look back 50 lines for definition of $varName
                    $defined = false;
                    for ($back = $i - 1; $back >= 0; $back--) {
                        if (is_array($tokens[$back])) {
                            if ($tokens[$back][0] === T_CLASS) break; // Don't go outside class
                            if ($tokens[$back][0] === T_VARIABLE && $tokens[$back][1] === $varName) {
                                $defined = true;
                                break;
                            }
                        }
                    }
                    if (!$defined && !in_array($varName, $superglobals)) {
                        echo $file->getFilename() . " line $useLine: Closure uses \$$varName which was NOT declared before closure!\n";
                    }
                }
            }
        }
    }
}
echo "Finished checking closure uses.\n";
