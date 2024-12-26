import os
import sys

def create_from_structure(destination_path, structure_file):
    """Crée une structure de dossiers et de fichiers à partir d'un fichier de structure."""

    if not os.path.exists(destination_path):
        os.makedirs(destination_path)

    with open(structure_file, 'r', encoding='utf-8') as f:
        lines = [line.rstrip() for line in f if line.strip()]

    def create_path(path, level):
        indent = " " * 4 * level
        
        if not path or path.startswith('├── ') or path.startswith('│   '):
            return None
        
        if path.startswith('└── '):
            path = path[4:]
        elif path.startswith('  '):
            path = path[2:]

        full_path = os.path.join(destination_path, path)
        
        if '.' in path:  # File
            try:
                dir_path = os.path.dirname(full_path)
                if not os.path.exists(dir_path):
                    os.makedirs(dir_path)
                open(full_path, 'a').close()  # Crée un fichier vide
                print(f"{indent}Created file: {full_path}")
            except Exception as e:
                print(f"Error creating file: {full_path}, Error: {e}")
        else:  # Directory
            try:
                os.makedirs(full_path, exist_ok=True)
                print(f"{indent}Created directory: {full_path}")
                
            except Exception as e:
                print(f"Error creating dir: {full_path}, Error: {e}")
            
        return full_path
    
    
    current_level = 0
    current_dir = None

    for line in lines:
        
        if line.startswith('├── ') or line.startswith('└── ') or line.startswith('│   ') or line.startswith('  '):
            
            if line.startswith('├── '):
                current_level = line.count('    ')
            elif line.startswith('└── '):
                current_level = line.count('    ')
            elif line.startswith('│   '):
                 current_level = line.count('    ')
            elif line.startswith('  '):
                 current_level = line.count('    ')
            

            create_path(line, current_level)
            
            
        elif line:
            current_dir= create_path(line,0)

if __name__ == "__main__":
    if len(sys.argv) != 3:
        print("Arguments"+sys.argv[1])
        print("Usage: python create_structure.py <destination_path> <structure_file>")
        sys.exit(1)

    destination_path = sys.argv[1]
    structure_file = sys.argv[2]
    
    create_from_structure(destination_path, structure_file)
    print("Structure created successfully.")