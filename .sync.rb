#!/usr/bin/env ruby
#encoding: utf-8
def loadphp file
  STDERR.puts "loading file: " << file
  content = File.open file, "r" do |io|
    io.read
  end
  result = {}
  content.scan(/^\s*(['"])([A-Za-z0-9_]*?)\1\s*=>\s*(['"])(.*?)\3,?\s*$/).each do |i|
    result[i[1]] = i[2] + i[3] + i[2]
  end
  result
end

def dumpphp file, list
  result = ""
  result << "<?php" << "\n"
  result << "return array(" << "\n"
  list.keys.sort.each do |k|
    result << "\t'" << k << "' => " << list[k] << ",\n"
  end
  result << ");"
  File.open file, "w" do |io|
    io.write result
  end
  STDERR.puts "saved file: " << file
end

ORIGIN = "zh-cn.php"
origin = loadphp ORIGIN

Dir["*.php"].each do |file|
  list = loadphp file
  result = {}
  (list.keys - origin.keys).each do |k|
    STDERR.puts "invalid key in #{file}: \"#{k}\" => #{list[k]}"
  end
  origin.keys.each do |k|
    if list[k]
      result[k] = list[k]
    else
      STDERR.puts "not found in #{file}: \"#{k}\". using origin."
      result[k] = origin[k]
    end
  end
  dumpphp file, result
end