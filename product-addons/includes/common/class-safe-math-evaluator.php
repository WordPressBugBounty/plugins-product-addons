<?php //phpcs:ignore
/**
 * Class SafeMathEvaluator
 *
 * @package WowAddons
 */

namespace PRAD\Includes\Common;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * SafeMathEvaluator class.
 */
class SafeMathEvaluator {
    
    public static function evaluate_expression($expression, $dynamic_variables = array()) {
        $expression = sanitize_text_field($expression);
        
        // Replace dynamic variables
        $processed_expression = preg_replace_callback(
            '/\{\{([a-zA-Z0-9_-]+)\}\}/',
            function ($matches) use ($dynamic_variables) {
                return !empty($dynamic_variables[$matches[1]]) ? $dynamic_variables[$matches[1]] : 0;
            },
            $expression
        );
        
        // Handle percentages: convert 'X%' to '(X/100)'
        $processed_expression = preg_replace('/(\d+(?:\.\d+)?)%/', '($1/100)', $processed_expression);
        
        try {
            $result = self::safe_evaluate($processed_expression);
            return is_numeric($result) && $result >= 0 ? (float) $result : 0;
        } catch (\Exception $e) {
            echo 'Error evaluating expression: ' . $e->getMessage();
            return 0;
        }
    }
    
     public static function safe_evaluate($expression) {
        // Remove whitespace
        $expression = preg_replace('/\s+/', '', $expression);
        
        // Validate expression contains only allowed characters
        if (!preg_match('/^[0-9+\-*\/().]+$/', $expression)) {
            throw new \Exception('Invalid characters in expression');
        }
        
        // Check for balanced parentheses
        if (!self::has_balanced_parentheses($expression)) {
            throw new \Exception('Unbalanced parentheses');
        }
        
        // Evaluate the expression
        return self::parse_expression($expression);
    }
    
     public static function has_balanced_parentheses($expression) {
        $count = 0;
        for ($i = 0; $i < strlen($expression); $i++) {
            if ($expression[$i] === '(') {
                $count++;
            } elseif ($expression[$i] === ')') {
                $count--;
                if ($count < 0) return false;
            }
        }
        return $count === 0;
    }
    
     public static function parse_expression($expression) {
        // Handle parentheses first
        while (strpos($expression, '(') !== false) {
            $expression = preg_replace_callback('/\(([^()]+)\)/', function($matches) {
                return self::evaluate_simple_expression($matches[1]);
            }, $expression);
        }
        
        return self::evaluate_simple_expression($expression);
    }
    
     public static function evaluate_simple_expression($expression) {
        // Handle multiplication and division first (left to right)
        while (preg_match('/(-?\d+(?:\.\d+)?)\s*([*\/])\s*(-?\d+(?:\.\d+)?)/', $expression, $matches)) {
            $left = (float) $matches[1];
            $operator = $matches[2];
            $right = (float) $matches[3];
            
            if ($operator === '*') {
                $result = $left * $right;
            } elseif ($operator === '/') {
                if ($right == 0) {
                    throw new \Exception('Division by zero');
                }
                $result = $left / $right;
            }
            
            $expression = str_replace($matches[0], $result, $expression);
        }
        
        // Handle addition and subtraction (left to right)
        while (preg_match('/(-?\d+(?:\.\d+)?)\s*([+\-])\s*(-?\d+(?:\.\d+)?)/', $expression, $matches)) {
            $left = (float) $matches[1];
            $operator = $matches[2];
            $right = (float) $matches[3];
            
            if ($operator === '+') {
                $result = $left + $right;
            } elseif ($operator === '-') {
                $result = $left - $right;
            }
            
            $expression = str_replace($matches[0], $result, $expression);
        }
        
        // Should be left with just a number
        if (!is_numeric($expression)) {
            throw new \Exception('Invalid expression result: ' . $expression);
        }
        
        return (float) $expression;
    }
}
