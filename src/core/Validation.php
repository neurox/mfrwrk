<?php

namespace Core;

/**
 * Validates the form data.
 */
class Validation {

  /**
   * Validation errors.
   *
   * @var array
   */
  private static $errors = [];

  /**
   * Validate form data.
   *
   * @param array $data
   *   Form data.
   * @param array $rules
   *   Validation rules.
   */
  public static function validate($data, $rules) {
    self::$errors = [];

    // Validate each field.
    foreach ($rules as $field => $ruleset) {
      $value = $data[$field] ?? NULL;
      $ruleParts = explode('|', $ruleset);
      $fieldName = self::getFieldName($field);

      // Validate each rule.
      foreach ($ruleParts as $rule) {
        if ($rule === 'csrf_token' && !self::validateCsrfToken($data['csrf_token'] ?? '')) {
          self::$errors[$field] = 'Formulario invalido, recargue la pagina y vuelva a intentarlo';
          break;
        }

        // Check for required rule.
        if ($rule === 'required' && empty($value)) {
          self::$errors[$field] = self::capStr("{$fieldName} es requerido");
          break;
        }

        // Check for min rule.
        if (strpos($rule, 'min:') === 0) {
          $min = substr($rule, 4);
          if (strlen($value) < $min) {
            self::$errors[$field] = self::capStr("El campo {$fieldName} debe tener al menos {$min} caracteres");
            break;
          }
        }

        // Check for email rule.
        if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
          self::$errors[$field] = self::capStr("El {$fieldName} debe ser valido");
          break;
        }

        // Check for alpha rule.
        if ($rule === 'alpha' && !preg_match('/^[\p{L} ]+$/u', $value)) {
          self::$errors[$field] = self::capStr("El campo {$fieldName} solo debe contener letras");
          break;
        }

        // Check for alnum rule.
        if ($rule === 'alnum' && !ctype_alnum($value)) {
          self::$errors[$field] = self::capStr("El campo {$fieldName} solo debe contener letras y numeros");
          break;
        }

        // Check that the two fields match.
        if (strpos($rule, 'same:') === 0) {
          $sameField = substr($rule, 5);
          $sameFieldName = self::getFieldName($sameField);
          if ($value !== $data[$sameField]) {
            self::$errors[$field] = self::capStr("Los campos {$sameFieldName} y {$fieldName} deben coincidir");
            break;
          }
        }
      }
    }

    return empty(self::$errors);
  }

  /**
   * Get field name.
   *
   * @param string $field
   *   Field name.
   *
   * @return string
   *   Field name.
   */
  public static function getFieldName($field) {
    // Field names.
    $fieldNames = [
      'csrf_token' => 'Formulario',
      'firstName' => 'Nombre',
      'lastName' => 'Apellido',
      'username' => 'Nombre de usuario',
      'email' => 'Email',
      'password' => 'Contraseña',
      'password_confirmation' => 'Confirmación de contraseña',
    ];

    return $fieldNames[$field] ?? $field;
  }

  /**
   * Capitalize a string.
   *
   * @param string $str
   *   String to capitalize.
   *
   * @return string
   *   Capitalized string.
   */
  public static function capStr($str) {
    return ucfirst(strtolower($str));
  }

  /**
   * Get validation errors.
   *
   * @return array
   *   Validation errors.
   */
  public static function getErrors() {
    return self::$errors;
  }

  /**
   * Generate a CSRF token.
   *
   * @return string
   *   CSRF token.
   */
  public static function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
      $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
  }

  /**
   * Validate a CSRF token.
   *
   * @param string $token
   *   The token to validate.
   *
   * @return bool
   *   True if the token is valid, false otherwise.
   */
  public static function validateCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
  }

}
