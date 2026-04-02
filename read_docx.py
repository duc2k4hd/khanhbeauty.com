import zipfile
import xml.etree.ElementTree as ET
import sys

def extract_text(docx_path):
    try:
        with zipfile.ZipFile(docx_path) as docx:
            xml_content = docx.read('word/document.xml')
            tree = ET.fromstring(xml_content)
            namespace = {'w': 'http://schemas.openxmlformats.org/wordprocessingml/2006/main'}
            text = []
            for t in tree.iter('{http://schemas.openxmlformats.org/wordprocessingml/2006/main}t'):
                if t.text:
                    text.append(t.text)
            return "\n".join(text)
    except Exception as e:
        return str(e)

if __name__ == "__main__":
    if len(sys.argv) > 1:
        text = extract_text(sys.argv[1])
        with open('docx_content.txt', 'w', encoding='utf-8') as f:
            f.write(text)
        print("Extracted to docx_content.txt")
    else:
        print("Usage: python read_docx.py <docx_path>")
