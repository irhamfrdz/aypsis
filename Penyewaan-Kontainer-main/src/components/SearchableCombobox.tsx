import React, { useState, useRef, useEffect } from 'react';
import { ChevronDown, X } from 'lucide-react';

interface SearchableComboboxProps {
  id?: string;
  value: string;
  onChange: (value: string) => void;
  options: string[];
  placeholder?: string;
  className?: string;
  inputClassName?: string;
  disabled?: boolean;
}

export default function SearchableCombobox({
  id,
  value,
  onChange,
  options,
  placeholder = 'Ketik atau pilih...',
  className = '',
  inputClassName = '',
  disabled = false,
}: SearchableComboboxProps) {
  const [isOpen, setIsOpen] = useState(false);
  const [highlightedIndex, setHighlightedIndex] = useState(-1);
  const containerRef = useRef<HTMLDivElement>(null);
  const inputRef = useRef<HTMLInputElement>(null);

  // Close dropdown on click outside
  useEffect(() => {
    function handleClickOutside(event: MouseEvent) {
      if (containerRef.current && !containerRef.current.contains(event.target as Node)) {
        setIsOpen(false);
      }
    }
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  // Filter options based on substring case-insensitive match
  const filteredOptions = React.useMemo(() => {
    if (!value.trim()) return options;
    const q = value.toLowerCase().trim();
    return options.filter((o) => o.toLowerCase().includes(q));
  }, [options, value]);

  // Reset highlighted index when options change
  useEffect(() => {
    setHighlightedIndex(-1);
  }, [filteredOptions]);

  const handleKeyDown = (e: React.KeyboardEvent<HTMLInputElement>) => {
    if (disabled) return;

    if (e.key === 'ArrowDown') {
      e.preventDefault();
      setIsOpen(true);
      setHighlightedIndex((prev) => (prev < filteredOptions.length - 1 ? prev + 1 : 0));
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      setIsOpen(true);
      setHighlightedIndex((prev) => (prev > 0 ? prev - 1 : filteredOptions.length - 1));
    } else if (e.key === 'Enter') {
      if (isOpen && highlightedIndex >= 0 && highlightedIndex < filteredOptions.length) {
        e.preventDefault();
        onChange(filteredOptions[highlightedIndex]);
        setIsOpen(false);
      } else {
        setIsOpen(false);
      }
    } else if (e.key === 'Escape') {
      setIsOpen(false);
    }
  };

  return (
    <div ref={containerRef} className={`relative ${className}`} id={id}>
      <div className="relative flex items-center">
        <input
          ref={inputRef}
          type="text"
          disabled={disabled}
          value={value}
          onChange={(e) => {
            onChange(e.target.value);
            setIsOpen(true);
          }}
          onFocus={() => {
            if (!disabled) setIsOpen(true);
          }}
          onKeyDown={handleKeyDown}
          placeholder={placeholder}
          className={`w-full text-xs font-mono border border-slate-200 rounded-xl pl-3 pr-8 py-2 bg-slate-50 text-slate-800 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 focus:outline-none transition-all ${
            disabled ? 'bg-slate-150 text-slate-400 cursor-not-allowed border-slate-200' : 'cursor-text'
          } ${inputClassName}`}
        />
        <div className="absolute right-2.5 flex items-center gap-1">
          {value && !disabled && (
            <button
              type="button"
              onClick={() => {
                onChange('');
                setIsOpen(true);
                inputRef.current?.focus();
              }}
              className="p-0.5 hover:bg-slate-200 rounded-full text-slate-400 hover:text-slate-600 transition-colors cursor-pointer"
            >
              <X className="w-3 h-3" />
            </button>
          )}
          <button
            type="button"
            disabled={disabled}
            onClick={() => {
              if (disabled) return;
              setIsOpen(!isOpen);
              inputRef.current?.focus();
            }}
            className="text-slate-400 hover:text-slate-600 transition-colors cursor-pointer"
          >
            <ChevronDown className="w-4 h-4 shrink-0" />
          </button>
        </div>
      </div>

      {isOpen && !disabled && filteredOptions.length > 0 && (
        <div className="absolute z-50 mt-1 w-full bg-white border border-slate-200 rounded-xl shadow-lg max-h-56 overflow-y-auto divide-y divide-slate-100/60">
          {filteredOptions.map((opt, idx) => (
            <button
              key={opt}
              type="button"
              onClick={() => {
                onChange(opt);
                setIsOpen(false);
              }}
              className={`w-full text-left px-3 py-2 text-xs font-mono transition-colors cursor-pointer flex items-center justify-between ${
                opt === value
                  ? 'bg-indigo-50 text-indigo-800 font-extrabold'
                  : idx === highlightedIndex
                  ? 'bg-slate-50 text-slate-900 font-semibold'
                  : 'hover:bg-slate-50 text-slate-700'
              }`}
            >
              <span>{opt}</span>
              {opt === value && (
                <span className="text-[9px] font-sans font-bold bg-indigo-100 text-indigo-700 py-0.5 px-1.5 rounded-full scale-90">Terpilih</span>
              )}
            </button>
          ))}
        </div>
      )}

      {isOpen && !disabled && filteredOptions.length === 0 && (
        <div className="absolute z-50 mt-1 w-full bg-white border border-slate-200 rounded-xl shadow-lg p-3 text-xs text-slate-400 text-center italic bg-slate-50/50">
          Belum ada draf yang cocok (ketik bebas untuk buat draf baru)
        </div>
      )}
    </div>
  );
}
