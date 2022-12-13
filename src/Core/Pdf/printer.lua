function tableComposite()
    for i=1, data['nombreComposite'],1 do
        sub = data["composite"][i]
        tex.print(sub .. " & \\fbox{" .. data[sub]["note"] .. "} & \\printChartResult{" .. data[sub]["echelle"] .. "}{" .. data[sub]["moyenne"] .. "} \\\\ ")
    end
end

function tableSimple(int, simple, nom)
  tex.print("\\begin{tabular}{m{0.3in} m{0.6in}}")
  tex.print(int .. " & " .. nom)
  tex.print("\\end{tabular}")
  tex.print(" & \\fbox{" .. data[simple][nom]["note"] .. "} & \\printChartResult{" .. data[simple][nom]["echelle"] .. "}{0} \\\\ ")
end

function createMinipage()
  tex.print("\\begin{minipage}{\\linewidth}")
end

function endMinipage()
  tex.print("\\end{minipage}")
end

function createTable()
  tex.print("\\begin{tabular}{m{1.8in} m{0.2in} m{4in}}")
end

function endTable()
  tex.print("\\end{tabular}")
end

function createBox()
  tex.print("\\noindent\\fbox{")
end

function endBox()
  tex.print("} \\\\")
end

function createScale()
  tex.print("& &")
  tex.print("\\begin{tabular}{m{0.28in} m{0.28in} m{0.28in} m{0.28in} m{0.28in} m{0.28in} m{0.28in} m{0.28in} m{0.28in} m{0.28in}}")
  tex.print("\\hfill I & \\hfill II & \\hfill III & \\hfill IV & \\hfill V & \\hfill VI & \\hfill VII & \\hfill VIII & \\hfill IX & \\hfill X")
  tex.print("\\end{tabular}")
  tex.print("\\\\")
end

function toLine()
  tex.print("\\vspace{1em}")
end