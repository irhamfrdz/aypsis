import React, { useState } from 'react';
import { parseInputDate } from '../utils';

interface FormDateInputProps {
  value: string;
  onChange: (val: string) => void;
  onBlur?: (parsedVal: string) => void;
  placeholder?: string;
  className?: string;
  id?: string;
}

export const FormDateInput: React.FC<FormDateInputProps> = ({
  value,
  onChange,
  onBlur,
  placeholder = 'dd/mm/yyyy',
  className,
  id
}) => {
  const [localVal, setLocalVal] = useState<string>('');

  // Synchronize state when value updates from outside (like YYYY-MM-DD)
  React.useEffect(() => {
    if (value) {
      if (/^\d{4}-\d{2}-\d{2}$/.test(value)) {
        const [y, m, d] = value.split('-');
        setLocalVal(`${d}/${m}/${y}`);
      } else {
        setLocalVal(value);
      }
    } else {
      setLocalVal('');
    }
  }, [value]);

  const handleBlur = () => {
    const trimmed = localVal.trim();
    if (!trimmed) {
      onChange('');
      if (onBlur) onBlur('');
      return;
    }

    const parsed = parseInputDate(trimmed);
    if (parsed) {
      onChange(parsed);
      if (onBlur) onBlur(parsed);
      // Format back to DD/MM/YYYY for local state
      const [y, m, d] = parsed.split('-');
      setLocalVal(`${d}/${m}/${y}`);
    } else {
      // If parsing failed (e.g. invalid string), keep what was typed and propagate
      onChange(trimmed);
      if (onBlur) onBlur(trimmed);
    }
  };

  return (
    <input
      id={id}
      type="text"
      value={localVal}
      placeholder={placeholder}
      onChange={(e) => setLocalVal(e.target.value)}
      onBlur={handleBlur}
      onKeyDown={(e) => {
        if (e.key === 'Enter') {
          (e.target as HTMLInputElement).blur();
        }
      }}
      className={className}
    />
  );
};
