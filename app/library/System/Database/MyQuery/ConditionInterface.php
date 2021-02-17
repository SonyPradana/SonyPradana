<?php

namespace System\Database\MyQuery;

interface ConditionInterface
{
  /**
   * Where statment dengan membandikan varibale,
   * compire bisa <, >, atau =
   *
   * @param string $bind key atau nama column
   * @param string $comparattion Tanda hubung untuk membadingkan
   * @param string $value Value atau nilai pembanding
   */
  public function compare(string $bind, string $comparation, string $value);

  /**
   * Where statment untuk membandinkan kesamaan variable (=)
   *
   * @param string $key Key atau nama column
   * @param string $value Value atau nilai dari key atau nama column
   */
  public function equal(string $bind, string $value);

  /**
   * Where statment untuk membandinkan kemiripan variable (LIKE)
   *
   * @param string $key Key atau nama column
   * @param string $value Value atau nilai dari key atau nama column
   */
  public function like(string $bind, string $value);

  /**
   * Costume where
   * (Warning: binding must include)
   *
   * @param string $where
   */
  public function where(string $where_condition, array $binder = null);

  /**
   * The BETWEEN operator is inclusive: begin and end values are included
   *
   * @param string $column_name Column name to be filter
   * @param string $val_1 Begin value range
   * @param string $val_2 End value range
   */
  public function between(string $column_name, string $val_1, string $val_2);

  /**
   * The IN operator is a shorthand for multiple OR conditions
   * @param string $column_name Column name
   * @param array $val String value match search
   */
  public function in(string $column_name, array $val);
}
